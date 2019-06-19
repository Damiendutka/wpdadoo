/**
 * Fonction reproduisant le var_dump de PHP pour un variable javascript
 *
 * @since    1.0.0
 * @param [array] [arr] [tableau à afficher]
 * @param {int} [level] [Profondeur courante du tableau pour la récursivité]
 */
function dump( arr, level ){
	var dumped_text = "";
	if( !level ) 
		level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for( var j=0; j<level+1; j++) 
		level_padding += "    ";
	
	if( typeof(arr) == 'object' ) 
	{ 	
		//Array/Hashes/Objects 
		for(var item in arr) 
		{
			var value = arr[item];
			if(typeof(value) == 'object') 
			{ 
				//If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} 
			else 
			{
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} 
	else 
	{ 
		//Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

/**
 * affiche dans la console le contenu d'un tableau
 *
 * @since    1.0.0
 * @param [array] [tableau] [tableau à afficher]
 * @param {string} [nomTableau] [Titre affiché dans la console]
 */
function n_print( tableau, nomTableau ){
    // console.clear();
    console.log('==================== ' + nomTableau);
    console.dir(tableau);
    jQuery.each(tableau, function(index, val) {
        console.log(nomTableau + ' : ' +  index + ' => ' + val);
    });
}

/**
 * Genere un tableau serialiser pour etre unserialize par du PHP
 *
 * @since    1.0.0
 */
function serialize(mixed_value){
	//  discuss at: http://phpjs.org/functions/serialize/
	// original by: Arpad Ray (mailto:arpad@php.net)
	// improved by: Dino
	// improved by: Le Torbi (http://www.letorbi.de/)
	// improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
	// bugfixed by: Andrej Pavlovic
	// bugfixed by: Garagoth
	// bugfixed by: Russell Walker (http://www.nbill.co.uk/)
	// bugfixed by: Jamie Beck (http://www.terabit.ca/)
	// bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net/)
	// bugfixed by: Ben (http://benblume.co.uk/)
	//    input by: DtTvB (http://dt.in.th/2008-09-16.string-length-in-bytes.html)
	//    input by: Martin (http://www.erlenwiese.de/)
	//        note: We feel the main purpose of this function should be to ease the transport of data between php & js
	//        note: Aiming for PHP-compatibility, we have to translate objects to arrays
	//   example 1: serialize(['Kevin', 'van', 'Zonneveld']);
	//   returns 1: 'a:3:{i:0;s:5:"Kevin";i:1;s:3:"van";i:2;s:9:"Zonneveld";}'
	//   example 2: serialize({firstName: 'Kevin', midName: 'van', surName: 'Zonneveld'});
	//   returns 2: 'a:3:{s:9:"firstName";s:5:"Kevin";s:7:"midName";s:3:"van";s:7:"surName";s:9:"Zonneveld";}'

	var val, key, okey,
	ktype = '',
	vals = '',
	count = 0,
	_utf8Size = function (str) {
		var size = 0,
		i = 0,
		l = str.length,
		code = '';
		for (i = 0; i < l; i++) 
		{
			code = str.charCodeAt(i);
			if (code < 0x0080) 
			{
				size += 1;
			} 
			else if (code < 0x0800) 
			{
				size += 2;
			} 
			else 
			{
				size += 3;
			}
		}
		return size;
	},
	_getType = function (inp) {
		var match, key, cons, types, type = typeof inp;

		if (type === 'object' && !inp) 
		{
			return 'null';
		}

		if (type === 'object') {
			if (!inp.constructor) 
			{
				return 'object';
			}
			cons = inp.constructor.toString();
			match = cons.match(/(\w+)\(/);
			if (match) 
			{
				cons = match[1].toLowerCase();
			}
			types = ['boolean', 'number', 'string', 'array'];
			for (key in types) 
			{
				if (cons == types[key]) 
				{
					type = types[key];
					break;
				}
			}
		}
		return type;
	},
	type = _getType(mixed_value);

	switch (type) 
	{
		case 'function':
			val = '';
			break;
		case 'boolean':
			val = 'b:' + (mixed_value ? '1' : '0');
			break;
		case 'number':
			val = (Math.round(mixed_value) == mixed_value ? 'i' : 'd') + ':' + mixed_value;
			break;
		case 'string':
			val = 's:' + _utf8Size(mixed_value) + ':"' + mixed_value + '"';
			break;
		case 'array':
		case 'object':
			val = 'a';
			/*
			    if (type === 'object') {
			      var objname = mixed_value.constructor.toString().match(/(\w+)\(\)/);
			      if (objname == undefined) {
			        return;
			      }
			      objname[1] = this.serialize(objname[1]);
			      val = 'O' + objname[1].substring(1, objname[1].length - 1);
			    }
			    */

			for (key in mixed_value) 
			{
				if (mixed_value.hasOwnProperty(key)) 
				{
					ktype = _getType(mixed_value[key]);
				if (ktype === 'function') 
				{
					continue;
				}

				okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
				vals += this.serialize(okey) + this.serialize(mixed_value[key]);
				count++;
				}
			}
			val += ':' + count + ':{' + vals + '}';
			break;
		case 'undefined':
			// Fall-through
		default:
			// if the JS object has a property which contains a null value, the string cannot be unserialized by PHP
			val = 'N';
			break;
	}
	if (type !== 'object' && type !== 'array') 
	{
		val += ';';
	}
	return val;
}

/**
 * Fonction transformant une chaîne de caractère en slug
 *
 * @since    1.0.0
 * @param string text 
 */
function slug( text ){
	return text.toLowerCase().replace(/[èéêë]/g, 'e').replace(/[ç]/g, 'c').replace(/[àâä]/g, 'a').replace(/[ïî]/g, 'i').replace(/[ûùü]/g, 'u').replace(/[ôöó]/g, 'o').replace(/ /g,'').replace(/[^\w-]+/g,'');
}

/**
 * Fonction convertissant un code hexadécimal en rgba
 *
 * @since    1.0.0
 */
function fHexaToRGBA(hex, opacity) 
{
	hex = hex.replace('#','');
    r = parseInt(hex.substring(0,2), 16);
    g = parseInt(hex.substring(2,4), 16);
    b = parseInt(hex.substring(4,6), 16);

    result = 'rgba('+r+','+g+','+b+','+opacity+')';
    return result;
}

/**
 * Corrige un problème sur l'utilsiation de fontawesome
 *
 * @since    1.0.0
 */
function fFontAwesomeToUnicode( name ){
	'use strict';

	// Create a holding element (they tend to use <i>, so let's do that)
	var testI = document.createElement('i');
	// Create a realistic classname
	// - maybe one day it will need both, so let's add them
	testI.className = 'fa ${name}';
	// We need to append it to the body for it to have
	//   its pseudo element created
	document.body.appendChild(testI);

	// Get the computed style
	var char = window.getComputedStyle(
	testI, ':before' // Add the ':before' to get the pseudo element
	).content.replace(/'|"/g, ''); // content wraps things in quotes
	                             //   which we don't want
	// Remove the test element
	testI.remove();


	var codeHex = char.charCodeAt(0).toString(16);
	while (codeHex.length < 4) {
	    codeHex = "0" + codeHex;
	}

	return codeHex;
}  

/**
 * Equivalent fStripSlashes en javascrip
 *
 * @since    1.0.0
 */
function fStripSlashes( str ){
	return str.replace(/\\'/g,'\'').replace(/\"/g,'"').replace(/\\\\/g,'\\').replace(/\\0/g,'\0');
}

/**
 * Equivalent isset en javascrip
 *
 * @since    1.0.0
 */
function isset(){
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: FremyCompany
    // +   improved by: Onno Marsman
    // *     example 1: isset( undefined, true);
    // *     returns 1: false
    // *     example 2: isset( 'Kevin van Zonneveld' );
    // *     returns 2: true
   
    var a=arguments; var l=a.length; var i=0;
   
    if (l==0) {
        throw new Error('Empty isset');
    }
   
    while (i!=l) {
        if (typeof(a[i])=='undefined' || a[i]===null) {
            return false;
        } else {
            i++;
        }
    }
    return true;
}

/**
 * unserialize string
 *
 * @since    1.8.0
 */
function unserialize2(serialize) {
	let obj = {};
	serialize = serialize.split('&');
	for (let i = 0; i < serialize.length; i++) {
		thisItem = serialize[i].split('=');
		obj[decodeURIComponent(thisItem[0])] = decodeURIComponent(thisItem[1]);
	};
	return obj;
};
