<!DOCTYPE html>
<html>
  <head>
    <script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="polymaps.js"></script>
    <style type="text/css">

@import url("example.css");

.layer circle {
  fill: lightgrey;
  stroke: white;
  fill-opacity: .75;
  vector-effect: non-scaling-stroke;

}

#copy {
  color: #000;
  opacity: .5;
}

#copy a {
  color: #000;
}

#title {
  position: absolute;
  right: 5px;
  bottom: 5px;
  color:white;

}

#start {
  position: absolute;
  left: 10px;
  bottom: 20px;  
}

#stop {
  position: absolute;
  left: 90px;
  bottom: 20px;
}

.clickable {
  cursor: pointer;
  cursor: hand;
}

.titleface {
color: yellow;
  padding:10px;
  border: 1px solid white;
  font-style:oblique;
  font-family:Tahoma;
  padding-right:10px;
  font-size: 1.2em;
}

#calendarDiv {
  font-family: Tahoma;
  color: yellow;
  position: absolute;
  right: 10px;
  top: 10px;
  opacity:0.8;
}

.selected {
  background: yellow;
  color: black;
}

.calendar {
    float: left;
    padding-right: 10px;
    font-size: 0.9em;
    text-align: center;
  }

  .smaller {
    font-size: 0.7em;
  }

    </style>
  </head>
  <body id="map">
    
    <div id="start" onclick="startAnimation();" class="clickable titleface">
      START
    </div>
    <div id="stop" onclick="stopTimer();" class="clickable titleface">
      STOP
    </div>

    <div id="title"></div>

    <div id="calendarDiv">
      <table class="calendar" cellspacing=0><tr><th>December</th></tr><tr><td><table class="smaller"><tr class=cl><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td id='date0'>1</td></tr><tr><td id='date1'>2</td><td id='date2'>3</td><td id='date3'>4</td><td id='date4'>5</td><td id='date5'>6</td><td id='date6'>7</td><td id='date7'>8</td></tr><tr><td id='date8'>9</td><td id='date9'>10</td><td id='date10'>11</td><td id='date11'>12</td><td id='date12'>13</td><td id='date13'>14</td><td id='date14'>15</td></tr><tr><td id='date15'>16</td><td id='date16'>17</td><td id='date17'>18</td><td id='date18'>19</td><td id='date19'>20</td><td id='date20'>21</td><td id='date21'>22</td></tr><tr><td id='date22'>23</td><td id='date23'>24</td><td id='date24'>25</td><td id='date25'>26</td><td id='date26'>27</td><td id='date27'>28</td><td id='date28'>29</td></tr><tr><td id='date29'>30</td><td id='date30'>31</td><td >&nbsp;</td><td>&nbsp;</td><td >&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr></table>  

<table class="calendar" cellspacing=0><tr><th>January</th></tr><tr><td><table class="smaller"><tr class=cl><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td id='date31'>1</td><td id='date32'>2</td><td id='date33'>3</td><td id='date34'>4</td><td id='date35'>5</td></tr><tr><td id='date36'>6</td><td id='date37'>7</td><td id='date38'>8</td><td id='date39'>9</td><td id='date40'>10</td><td id='date41'>11</td><td id='date42'>12</td></tr><tr><td id='date43'>13</td><td id='date44'>14</td><td id='date45'>15</td><td id='date46'>16</td><td id='date47'>17</td><td id='date48'>18</td><td id='date49'>19</td></tr><tr><td id='date50'>20</td><td id='date51'>21</td><td id='date52'>22</td><td id='date53'>23</td><td id='date54'>24</td><td id='date55'>25</td><td id='date56'>26</td></tr><tr><td id='date57'>27</td><td id='date58'>28</td><td id='date59'>29</td><td id='date60'>30</td><td id='date61'>31</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr></table>


<table class="calendar" cellspacing=0><tr><th>February</th></tr><tr><td><table class="smaller"><tr class=cl><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td id='date62'>1</td><td id='date63'>2</td></tr><tr><td id='date64'>3</td><td id='date65'>4</td><td id='date66'>5</td><td id='date67'>6</td><td id='date68'>7</td><td id='date69'>8</td><td id='date70'>9</td></tr><tr><td id='date71'>10</td><td id='date72'>11</td><td id='date73'>12</td><td id='date74'>13</td><td id='date75'>14</td><td id='date76'>15</td><td id='date77'>16</td></tr><tr><td id='date78'>17</td><td id='date79'>18</td><td id='date80'>19</td><td id='date81'>20</td><td id='date82'>21</td><td id='date83'>22</td><td id='date84'>23</td></tr><tr><td id='date85'>24</td><td id='date86'>25</td><td id='date87'>26</td><td id='date88'>27</td><td id='date89'>28</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr></table>


<table class="calendar" cellspacing=0><tr><th>March</th></tr><tr><td><table class="smaller"><tr class=cl><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td id='date90'>1</td><td id='date91'>2</td></tr><tr><td id='date92'>3</td><td id='date93'>4</td><td id='date94'>5</td><td id='date95'>6</td><td id='date96'>7</td><td id='date97'>8</td><td id='date98'>9</td></tr><tr><td id='date99'>10</td><td id='date100'>11</td><td id='date101'>12</td><td id='date102'>13</td><td id='date103'>14</td><td id='date104'>15</td><td id='date105'>16</td></tr><tr><td id='date106'>17</td><td id='date107'>18</td><td id='date108'>19</td><td id='date109'>20</td><td id='date110'>21</td><td id='date111'>22</td><td id='date112'>23</td></tr><tr><td id='date113'>24</td><td id='date114'>25</td><td id='date115'>26</td><td id='date116'>27</td><td id='date117'>28</td><td id='date118'>29</td><td id='date119'>30</td></tr><tr><td id='date120'>31</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr></table>


<table class="calendar" cellspacing=0><tr><th>April</th></tr><tr><td><table class="smaller"><tr class=cl><td>Su</td><td>Mo</td><td>Tu</td><td>We</td><td>Th</td><td>Fr</td><td>Sa</td></tr><tr><td>&nbsp;</td><td id='date121'>1</td><td id='date122'>2</td><td id='date123'>3</td><td id='date124'>4</td><td id='date125'>5</td><td id='date126'>6</td></tr><tr><td id='date127'>7</td><td id='date128'>8</td><td id='date129'>9</td><td id='date130'>10</td><td id='date131'>11</td><td id='date132'>12</td><td id='date133'>13</td></tr><tr><td id='date134'>14</td><td id='date135'>15</td><td id='date136'>16</td><td id='date137'>17</td><td id='date138'>18</td><td id='date139'>19</td><td id='date140'>20</td></tr><tr><td id='date141'>21</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr></table></td></tr></table>

    </div>
    <span id="copy">
      &copy; 2010
      <a href="http://www.cloudmade.com/">CloudMade</a>,
      <a href="http://www.openstreetmap.org/">OpenStreetMap</a> contributors,
      <a href="http://creativecommons.org/licenses/by-sa/2.0/">CCBYSA</a>.
    </span>

  <script type="text/javascript">
      
      var allDatesData = (function () {
        var allDatesData = null;
        $.ajax({
            'async': false,
            'global': false,
            'url': "allDatesData.json",
            'dataType': "json",
            'success': function (data) {
                allDatesData = data;
            }
        });
        return allDatesData;
    })(); 
      var dayIndex = 0;

      var po = org.polymaps;
      var jsonData = null;
      var newJsonData = null;

      var map = po.map()
          .container(document.getElementById("map").appendChild(po.svg("svg")))
          .add(po.interact())
          .add(po.hash())
          .center({lat: 46.14, lon: -101.26})
          .zoom(4);

      map.add(po.image()
          .url(po.url("http://{S}tile.cloudmade.com"
          + "/9c01b06470084ec3b50e8de888a13e89" // http://cloudmade.com/register
          + "/999/256/{Z}/{X}/{Y}.png")
          .hosts(["a.", "b.", "c.", ""])));

      map.add(po.compass().pan("none"));

      var myVar;
      
      refreshData(dayIndex++);

      //set up click actions
      for (i=0; i<allDatesData.length; i++) {
        $('#date' + i).click(selectDate);
        $('#date' + i).addClass('clickable');
      }

  function startAnimation() {
    myVar=setInterval(function(){refreshData(dayIndex++)},500);
  }

  function refreshData(i) {
    if (i < allDatesData.length) {
      if (jsonData) {
        map.remove(jsonData)
        unhighlightDate(i-1);
      }
      jsonData = po.geoJson()
        .features(allDatesData[i]['features'])
        .on("load", load)
        .clip(false);
      map.add(jsonData);

      $('#date' + i).addClass('selected');

    } else {
      stopTimer();
    }

  }

  function hideLayer(layer) {
     map.remove(layer);
  }

  function stopTimer() {
    clearInterval(myVar);
  }

  function updateCaption(date) {
    $("#title").text(date);
  }

  function load(e) {
    for ( var i in e.tile.element.childNodes ) {
      var el = e.tile.element.childNodes[i];
      var val = e.features[i];
      if (val && val.data) {
        count = val.data.properties.count;
        r = 3 * count;
        //if (count > 10000) r = 6;
        //else if (count > 5000) r = 4; 

        el.setAttribute("r", r);
      }
    }
  }
    
    function updateData() {
      jsonData = po.geoJson()
          //.url("wsis-sample.json")
          .features(singleDateData)
          .on("load", load)
          .clip(false)
          .reshow();
    }

    function unhighlightDate(dateIndex) {
      $('#date' + dateIndex).removeClass('selected');
    }

    function selectDate(){
      unhighlightDate(dayIndex - 1);
      elementId = this.id;
      dayIndex = elementId.substring(4);
      refreshData(dayIndex++);
    }

    </script>

    <script type="text/javascript">

      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-35552544-1']);
        _gaq.push(['_trackPageview']);
      
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();

    </script>
  </body>
</html>
