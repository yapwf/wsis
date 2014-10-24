'use strict';

define(['handlebars'],
    function (Handlebars) {

        var pixelWidthSmall = 30;
        var pixelWidthWide = 60;
        var offsetIndexSmall = 24;
        var offsetIndexLarge = 30;
        //define Handlebars helper functions

        /*
        //OVERWRITE HANDLEBARS IF
        Handlebars.registerHelper('if', function(conditional, options) {
            if (Handlebars.Utils.isFunction(conditional)) { conditional = conditional.call(this); }

            // Default behavior is to render the positive path if the value is truthy and not empty.
            // The `includeZero` option may be set to treat the condtional as purely not empty based on the
            // behavior of isEmpty. Effectively this determines if 0 is handled by the positive path or negative.
            if ((!options.hash.includeZero && !conditional) || Handlebars.Utils.isEmpty(conditional) || conditional === '--') {
                return options.inverse(this);
            } else {
                return options.fn(this);
            }
        });*/

        Handlebars.registerHelper('dateFormat', function(text){            
            var time = text;
            time = time.split(' ')[0];
            time = time.split('-')[1] + '/' + time.split('-')[2] + '/' + time.split('-')[0];
            return time;
            
        });

        Handlebars.registerHelper('americanDateFormat', function(text){
            return text;
        });
        
        Handlebars.registerHelper('calculateRatingWidth', function (ratingText, size) {
            var width = parseInt(ratingText);
            if (size && size === 'large') 
                width *= pixelWidthWide;
            else 
                width *= pixelWidthSmall;
            return width;
        });

        Handlebars.registerHelper('calculateFreezingLevelOffset', function(rating, freezingLevel, size){
            var offsetIndex = offsetIndexSmall;
            if (size && size === 'large') 
                offsetIndex = offsetIndexLarge;

            var offset = -offsetIndex + (offsetIndex/4) * (parseInt(rating)-1);
            return offset;
        });

        Handlebars.registerHelper('feetOrMeters', function(text){
            if (text === 'T' || text === 't') {
                return "ft";
            } else {
                return "m";
            }
        })

    }
        
);
