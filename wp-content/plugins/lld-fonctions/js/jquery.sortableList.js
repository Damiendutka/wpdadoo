/**
 * JQuery Shopping List ( http://tuts.guillaumevoisin.fr/jquery/sortableList/ ) 
 * Copyright (c) Guillaume Voisin 2010
 */
 
(function($){
	$.fn.sortableList = function(options) {

		// Options par defaut
		var defaults = {};
				
		var options = $.extend(defaults, options);
		
		this.each(function(){
				
			var obj = $(this);
			
			// Emp�cher la s�lection des �l�ments � la sourirs (meilleure gestion du drag & drop)
			var _preventDefault = function(evt) { evt.preventDefault(); };
			$("li").bind("dragstart", _preventDefault).bind("selectstart", _preventDefault);

			if ( options['trash'] )
				containment = '.sortableList';
			else
				containment = '.ui-sortable';

			// Initialisation du composant "sortable"
			if ( options['sortable'] )
			{
				$(obj).sortable({
					axis: "y", // Le sortable ne s'applique que sur l'axe vertical
					containment: containment, // Le drag ne peut sortir de l'�l�ment qui contient la liste
					handle: "", // Le drag ne peut se faire que sur l'�l�ment .item (le texte)
					distance: 10, // Le drag ne commence qu'� partir de 10px de distance de l'�l�ment
					// Evenement appel� lorsque l'�l�ment est relach�
					stop: function(event, ui){
						// Pour chaque item de liste
						$(obj).find("li").each(function(){
							// On actualise sa position
							index = parseInt($(this).index()+1);
							// On la met � jour dans la page
							// $(this).find(".count").text(index);
						});
					}
				});
			}

			var emptyList = $(obj).children().length == 0 ? ' empty' : '';

			// Ajout de l'�l�ment Poubelle � notre liste
			if ( options['trash'] )
				$(obj).after("<div class='trash'><i class='fa fa-trash'></i>&nbsp;" + translationObject.trash + "</div>");
			
			var htmlToAdd = "<div class='add" + emptyList + "'>";
			htmlToAdd += "<input id='addValue' placeholder='" + translationObject.title + "' />"; // Ajout du champ principal de la liste
			if ( options['link'] )
				htmlToAdd += " <input id='addUrl' placeholder='http://' />"; // Ajout du champ lien de la liste
			if ( options['newtab'] )
				htmlToAdd += "<input id='newtab' type='checkbox' value='1' /> <label for='newtab'>" + translationObject.newTab + "</label>"; // Ajout du champ nouvel onglet
			htmlToAdd += "<br /><br /><input type='button' value='" + translationObject.addItem + "' id='addBtn' class='button' /> "; // Ajout du bouton d'ajout
			htmlToAdd += "</div>";

			$(obj).after(htmlToAdd);

			// Action de la poubelle
			// Initialisation du composant Droppable
			if ( options['trash'] )
			{
				$(".trash").droppable({
					// Lorsque l'on relache un �l�ment sur la poubelle
					drop: function(event, ui){
						// On retire la classe "hover" associ�e au div .trash
						$(this).removeClass("hover");
						// On ajoute la classe "deleted" au div .trash pour signifier que l'�l�ment a bien �t� supprim�
						$(this).addClass("deleted");
						// On affiche un petit message "Cet �l�ment a �t� supprim�" en r�cup�rant la valeur textuelle de l'�l�ment relach�
						$(this).text(ui.draggable.find(".item").text()+" removed !");
						// On supprimer l'�l�ment de la page, le setTimeout est un fix pour IE (http://dev.jqueryui.com/ticket/4088)
						setTimeout(function() { ui.draggable.remove(); }, 1);
						
						// On retourne � l'�tat originel de la poubelle apr�s 2000 ms soit 2 secondes
						elt = $(this);
						setTimeout(function(){ elt.removeClass("deleted"); elt.html("<i class='fa fa-trash'></i>&nbsp;" + translationObject.trash); }, 2000);
					},
					// Lorsque l'on passe un �l�ment au dessus de la poubelle
					over: function(event, ui){
						// On ajoute la classe "hover" au div .trash
						$(this).addClass("hover");
						// On cache l'�l�ment d�plac�
						ui.draggable.hide();
						// On indique via un petit message si l'on veut bien supprimer cet �l�ment
						$(this).text("Remove "+ui.draggable.find(".item").text());
						// On change le curseur
						$(this).css("cursor", "pointer");
					},
					// Lorsque l'on quitte la poubelle
					out: function(event, ui){
						// On retire la classe "hover" au div .trash
						$(this).removeClass("hover");
						// On r�affiche l'�l�ment d�plac�
						ui.draggable.show();
						// On remet le texte par d�faut
						$(this).text(translationObject.trash);
						// Ainsi que le curseur par d�faut
						$(this).css("cursor", "normal");
					}
				})
			}

			/*
			* Ajouter les controles sur le bouton "ajouter"
			*
			* @Return void
			*/
			
			// Bouton ajouter
			$("#addBtn").click(function(){
				// Si le texte n'est pas vide
				if( $("#addValue").val() != "" )
				{
					if ( $(obj).parent().find('.add').hasClass('empty') )
						$(obj).parent().find('.add').removeClass('empty');

					// On ajoute un nouvel item � notre liste
					$(obj).append('<li data-url="' + $("#addUrl").val()+ '" data-newtab="' + $("#newtab").is(':checked')+  '">' + $("#addValue").val() + '</li>');
					// On r�initialise le champ de texte pour l'ajout
					$("#addValue").val("");

					if ( options['link'] )
						$("#addUrl").val("");

					if ( options['newtab'] )
						$("#newtab").attr("checked", false);

					// On ajoute les contr�les � notre nouvel item
					addControls($(obj).find("li:last-child"));

					// Trigger update
					var tabListTitles = fGetListTitles();
					$('.sortableList ul').trigger( "update", tabListTitles );		
				}
			})
			// On autorise �galement la validation de la saisie d'un nouvel item par pression de la touche entr�e
			$("#addValue, #addUrl").live("keyup", function(e) {
				if(e.keyCode == 13) {
					// On lance l'�v�nement click associ� au bouton d'ajout d'item
					$("#addBtn").trigger("click");
				}
			});
			
			// Pour chaque �l�ment trouv� dans la liste de d�part
			$(obj).find("li").each(function(){
				// On ajoute les contr�les
				addControls($(this));
			});

		});
				
		/*
		* Fonction qui ajoute les contr�les aux items
		* @Param�tres
		*  - elt: �l�ment courant (liste courante)
		*
		* @Return void
		*/
		
		function addControls(elt)
		{
			var textItem = $(elt).text();

			// On ajoute en premier l'�l�ment textuel
			var itemClass = '';
			if ( options['sortable'] )
				itemClass = 'sortable';

			$(elt).html("<span class='item " + itemClass + " '>" + textItem + "<i class='edit fa fa-pencil'></i></span>");

			// Puis l'url
			if ( options['link'] )
			{
				if ( $(elt).data('url') != '' )
					$(elt).append("<span class='url'>" + $(elt).data('url') + "<i class='edit fa fa-pencil'></i></span>");
			}

			var position = parseInt($(elt).index()+1);

			// Open in a new tab
			if ( options['newtab'] )
			{
				if ( $(elt).data('url') != '' )
				{
					if ( $(elt).data('newtab') )
						var checked = "checked='checked'";
					else
						var checked = '';

					$(elt).append("<span class='newtab'><input type='checkbox' id='newtab-" + slug(textItem) + "' class='edit' value='1'" + checked + " /> <label for='newtab-" + slug(textItem) + "'>" + translationObject.newTab + "</label></span>");
				}
			}

			// Puis le champ � enregistrer

			$(elt).append('<input type="hidden" class="sortableField title" name="sortableField[' + slug(textItem) + '][title]" value="' + textItem.replace(/"/g, '&quot;') + '" />');
			
			if ( options['link'] )
				$(elt).append('<input type="hidden" class="sortableField url" name="sortableField[' + slug(textItem) + '][url]" value="' + $(elt).data('url') + '" />');
			
			if ( options['newtab'] )
				$(elt).append('<input type="hidden" class="sortableField newtab" name="sortableField[' + slug(textItem) + '][newtab]" value="' + $(elt).data('newtab') + '" />');
           
			// Puis l'�l�ment de position
			// $(elt).prepend('<span class="count">'+parseInt($(elt).index()+1)+'</span>');

			if ( options['remove'] )
				$(elt).prepend('<span class="remove"><i class="fa fa-remove"></i></span>');

			if ( options['checkbox'] )
				$(elt).prepend('<input type="checkbox" class="check unchecked"/>');
			
			// Au clic sur cet �l�ment
			$(elt).find(".check").click(function(){
				// On alterne la classe de l'item (le <li>), le CSS associ� fera que l'�l�ment sera barr�
				$(this).parent().toggleClass("bought");
				
				// Si cet �l�ment est achet�
				if($(this).parent().hasClass("bought"))
					// On modifie la classe en ajoutant la classe "checked"
					$(this).removeClass("unchecked").addClass("checked");
				// Le cas contraire
				else
					// On modifie la classe en retirant la classe "checked"
					$(this).removeClass("checked").addClass("unchecked");
			});

			// Au clic sur la suppression
			$(elt).find(".remove").click(function(){

				if ( $(elt).parent().children().length-1 == 0 )
					$(elt).parent().parent().find('.add').addClass('empty');

				elt.remove();

				// Trigger update
				var tabListTitles = fGetListTitles();
				$('.sortableList ul').trigger( "update", tabListTitles );		
			});


			$(elt).on('click', '.edit', {}, function(){
				$(this).parent().trigger('dblclick');
			});
			
			// Au double clic sur le texte
			$(elt).find(".item").dblclick(function(){
				// On r�cup�re sa valeur
				txt = $(this).text().replace(/"/g, '&quot;');
				// url = $(this).parent().data('url');

				// On ajoute un champ de saisie avec la valeur
				$(this).html('<input value="'+txt+'" size="40" />');
				// $(this).append("<input value='"+url+"' />");

				// On la s�lectionne par d�faut
				$(this).find("input").select();
			})

			// Au double clic sur le lien
			$(elt).find(".url").dblclick(function(){
				// On r�cup�re sa valeur
				url = $(this).parent().attr('data-url');

				// On ajoute un champ de saisie avec la valeur
				$(this).html("<input value='"+url+"' size='40' />");
 
				// On la s�lectionne par d�faut
				$(this).find("input").select();
			})
			
			// Lorsque l'on quitte la zone de saisie du texte
			$(elt).find(".item input").live("blur", function(){
				// On r�cup�re la valeur du champ de saisie
				txt = $(this).val();
				// On ins�re dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().find('.sortableField.title').val(txt);				
				$(this).parent().html(txt + "<i class='edit fa fa-pencil'></i>");

				// Trigger update
				var tabListTitles = fGetListTitles();
				$('.sortableList ul').trigger( "update", tabListTitles );		
			})

			// Lorsque l'on quitte la zone de saisie du lien
			$(elt).find(".url input").live("blur", function(){
				// On r�cup�re la valeur du champ de saisie
				url = $(this).val();

				// On ins�re dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().attr("data-url", url);
				$(this).parent().parent().find('.sortableField.url').val(url);				
				$(this).parent().html(url + "<i class='edit fa fa-pencil'></i>");
				
			});

			// When clicking on checkbox "Open in a new tab"
			$(elt).find(".newtab .edit").on("click", function(){
				// On r�cup�re la valeur du champ de saisie
				newtab = $(this).is(':checked');

				// On ins�re dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().attr("data-newtab", newtab);
				$(this).parent().parent().find('.sortableField.newtab').val(newtab);
			})
			
			// On autorise la m�me action lorsque l'on valide par la touche entr�e
			$(elt).find(".item input").live("keyup", function(e) {
				if(e.keyCode == 13) {
					$(this).trigger("blur");
				}
			});
		}

		function fGetListTitles()
		{
			tabListTitles = new Array();

			jQuery( ".sortableList ul .sortableField" ).each( function(i) {
				tabListTitles[i] = jQuery(this).val();
			});

			return Array(tabListTitles);
		}
		
		// On continue le chainage JQuery
		return this;
	};
})(jQuery);