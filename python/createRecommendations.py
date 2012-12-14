import urllib2
import simplejson
import datetime
import time
import _mysql
import nwsWeather
import resortMaster
import os
import uuid
import snowforecastWeather

PCT_LEVER_NEW_SNOW = 0.6
PCT_LEVEL_PREV_SNOW = 0.2
PCT_LEVEL_PROJ_SNOW = 0.2
PCT_FACTOR_PREV_SNOW = 0.4
UPPER_LIMIT = 16
POWDER_STAR_RATING_CUTOFF = [ 2.0, 5.0, 7.5, 10.0 ]
COUCH_DB_SERVER = "http://localhost:5984"
COUCH_DB_NAME = "recommendations"
COUCH_DB_URL = COUCH_DB_SERVER + "/" + COUCH_DB_NAME 
REC_POWDER = "Powder"

#Bluebird summaries
BLUEBIRD_SUMMARIES = {}
BLUEBIRD_SUMMARIES[5] = ["Sunny", "Clear"]
BLUEBIRD_SUMMARIES[4] = ["Mostly Sunny","Mostly Clear", "some clouds"]
BLUEBIRD_SUMMARIES[3] = ["Partly Cloudy","Partly Sunny"]
BLUEBIRD_SUMMARIES[2] = ["Mostly Cloudy", "Increasing Clouds", "light snow"]

def calcAverage(value1, value2) :
	return (float(value1) + float(value2)) / 2

def checkUpperLimit(snowfallAmount) :
	if snowfallAmount > UPPER_LIMIT :
		return UPPER_LIMIT
	return snowfallAmount

def formatFloat(inputNum) :
	return "%.1f" % float(inputNum) 

def calcPowder(new_snow, previous_snow, projected_snow) :
	previous_snow = checkUpperLimit(previous_snow - new_snow)
	projected_snow = checkUpperLimit(projected_snow)
	
	adjustedSnow = new_snow * PCT_LEVER_NEW_SNOW + previous_snow * PCT_LEVEL_PREV_SNOW * PCT_FACTOR_PREV_SNOW + projected_snow * PCT_LEVEL_PROJ_SNOW
	starRating = 1
	for cutoff in POWDER_STAR_RATING_CUTOFF : 
		if adjustedSnow < cutoff :
			break
		starRating += 1
	return starRating 

def calcBluebird(weatherSummary) :
	for rating,summaries in BLUEBIRD_SUMMARIES.iteritems() :
		for summary in summaries :
			if (summary.lower() == weatherSummary.lower()) :
				return rating

	return 1


def createRecommendationDocument(resort, recDate ) :
	rec = {}
	rec['resort'] = resort['name'].lower().replace(' ','')
	rec['resort_name'] = resort['name']
	rec['state'] = resort['state']
	rec['region'] = resort['region']
	location = {}
	location['lat'] = float(resort['latitude'])
	location['lon'] = float(resort['longitude'])
	rec['location'] = location

	rec['createdOn'] = str(datetime.date.today())
	rec['date'] = recDate

	return rec


def calculateRecommendation(dateOfRecommendation, resort, db) :
	previousDay = dateOfRecommendation - datetime.timedelta(days=1)
	nextDay = dateOfRecommendation + datetime.timedelta(days=1)

	###Get the amount of fresh snow for tomorrow
	newSnowForTomorrowSf = snowforecastWeather.getTotalSnowfallForRangeForResort(previousDay, dateOfRecommendation, resort['id'], db)
	#check if domestic, if not, just use the totals from SnowForecast
	newSnowForTomorrowNws = newSnowForTomorrowSf
	if (resort['domestic'] == 'T') :
		newSnowForTomorrowNws = nwsWeather.getTotalSnowfallForRangeForResort(previousDay, dateOfRecommendation, resort['id'], db)
	#Get the average of NWS and SF
	newSnowForTomorrow = calcAverage(newSnowForTomorrowNws, newSnowForTomorrowSf)
	
	###Get the projected snow for tomorrow
	projectedSnowTomorrowSf = snowforecastWeather.getTotalSnowfallForRangeForResort(dateOfRecommendation, nextDay, resort['id'], db, True)
	#check if domestic, if not, just use the totals from SnowForecast
	projectedSnowTomorrowNws = projectedSnowTomorrowSf
	if (resort['domestic'] == 'T') :
		projectedSnowTomorrowNws = nwsWeather.getTotalSnowfallForRangeForResort(dateOfRecommendation, nextDay, resort['id'], db, True)
	#Get the average of NWS and SF
	projectedSnowTomorrow = calcAverage(projectedSnowTomorrowNws, projectedSnowTomorrowSf) 
	
	#TODO - update this to read from actual resortMaster
	previousSnowFallSf = snowforecastWeather.getTotalSnowfallForRangeForResort(dateOfRecommendation - datetime.timedelta(days=3), dateOfRecommendation, resort['id'], db)
	if (previousSnowFallSf == None) :
		previousSnowFallSf = 0
	previousSnowFallNws = previousSnowFallSf	
	#check if domestic, if not just use the totals from SnowForecast
	if (resort['domestic'] == 'T') :
		previousSnowFallNws = nwsWeather.getTotalSnowfallForRangeForResort(dateOfRecommendation - datetime.timedelta(days=3), dateOfRecommendation, resort['id'], db)
		if previousSnowFallNws == None :
			previousSnowFallNws = 0

	previousSnowFall = calcAverage(previousSnowFallSf, previousSnowFallNws)

	docId = resort['name'].lower().replace(' ','') + "_" + str(dateOfRecommendation)
	docUrl = COUCH_DB_URL + "/" + docId
	
	#Create the base recommendation document
	reccomendationDocument = createRecommendationDocument(resort, str(dateOfRecommendation) )
	
	#Create Powder Forecast first
	powderData = {}
	powderData['snow_prev'] = formatFloat(previousSnowFall)

	powderData['snow_new'] = formatFloat(newSnowForTomorrow)
	powderData['snow_forecast'] = formatFloat(projectedSnowTomorrow)
	
	powderData['rating'] = calcPowder(newSnowForTomorrow, previousSnowFall, projectedSnowTomorrow)
	reccomendationDocument['powder'] = powderData

	#Now create the bluebird recommendation
	bluebirdData = {}
	sfBluebirdAM = calcBluebird(snowforecastWeather.getWeatherSummaryForDate(dateOfRecommendation, resort['id'], db)[0][0])
	sfBluebirdPM = calcBluebird(snowforecastWeather.getWeatherSummaryForDate(dateOfRecommendation, resort['id'], db)[1][0])

	#Check if domestic, if not, do not pull from NWS
	nwsBluebirdRating = calcAverage(sfBluebirdAM, sfBluebirdPM)
	if (resort['domestic'] == 'T') :
		weatherRecord = nwsWeather.getWeatherSummaryForDate(dateOfRecommendation, resort['id'], db)
		bluebirdData['weather_summary'] = weatherRecord[1]
		nwsBluebirdRating = calcBluebird(weatherRecord[0])
	
	bluebirdData['rating'] = int(calcAverage(nwsBluebirdRating, calcAverage(sfBluebirdAM, sfBluebirdPM)) + 0.49)
	reccomendationDocument['bluebird'] = bluebirdData

	#check if we need to override the existing record
	try :	
		currentDocLocationResponse = simplejson.load(urllib2.urlopen(docUrl))
		reccomendationDocument['_rev'] = str(currentDocLocationResponse['_rev'])
	except urllib2.HTTPError:
		print docId + " not found, will create a new record"
	#print id

	curlCommand = "curl -X PUT " + COUCH_DB_URL + "/" + docId + " -H 'Content-Type: application/json' -d " + "'" + simplejson.dumps(reccomendationDocument) + "'"
	#print curlCommand
	os.system(curlCommand)
 
#Connect to DB
#db = _mysql.connect("localhost","wsis","wsis","wsis")

def create(db) :
	#retrieve the master list of resorts
	resorts = resortMaster.getResorts(db)

	#for each resort, calculate the next several days out
	for resort in resorts :
		for i in range(1,6) :
			calculateRecommendation(datetime.date.today() + datetime.timedelta(days=i), resort, db)
	

#db.close()



