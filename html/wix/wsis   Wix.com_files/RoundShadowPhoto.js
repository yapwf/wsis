W.Skins.newSkin({name:"wysiwyg.viewer.skins.photo.RoundShadowPhoto",Class:{Extends:"mobile.core.skins.BaseSkin",_params:[{id:"tdr",type:Constants.SkinParamTypes.URL,defaultTheme:"BASE_THEME_DIRECTORY"},{id:"xxx",type:Constants.SkinParamTypes.COLOR_ALPHA,defaultTheme:"color_11",noshow:true},{id:"rd",type:Constants.SkinParamTypes.BORDER_RADIUS,defaultValue:"5px"},{id:"brd",type:Constants.SkinParamTypes.COLOR_ALPHA,defaultTheme:"color_15"},{id:"brw",type:Constants.SkinParamTypes.SIZE,defaultValue:"2px"},{id:"shd",type:Constants.SkinParamTypes.BOX_SHADOW,defaultValue:"0 1px 3px rgba(0, 0, 0, 0.5);"},{id:"pos",type:Constants.SkinParamTypes.OTHER,defaultValue:"position:absolute; top:0; bottom:0; left:0; right:0;"}],_html:'<a skinPart="link"><div skinPart="wrp"><div skinPart="img" skin="mobile.core.skins.ImageSkin"></div></div><div class="xxx"></div></a>',_css:["{[rd][shd]}","%link% { display:block; [pos]}","%wrp%  { [pos][rd][shd]background-color:[brd]; border:[brw] solid [brd]; overflow:hidden;} background: [xxx] url([tdr]net.png) center center repeat;","%.xxx%  { [pos] background: url([tdr]net.png) center center repeat;}","%img%  { [rd]height:100%; }"]}});