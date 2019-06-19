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
			
			// Empêcher la sélection des éléments à la sourirs (meilleure gestion du drag & drop)
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
					containment: containment, // Le drag ne peut sortir de l'élément qui contient la liste
					handle: "", // Le drag ne peut se faire que sur l'élément .item (le texte)
					distance: 10, // Le drag ne commence qu'à partir de 10px de distance de l'élément
					// Evenement appelé lorsque l'élément est relaché
					stop: function(event, ui){
						// Pour chaque item de liste
						$(obj).find("li").each(function(){
							// On actualise sa position
							index = parseInt($(this).index()+1);
							// On la met à jour dans la page
							// $(this).find(".count").text(index);
						});
					}
				});
			}

			var emptyList = $(obj).children().length == 0 ? ' empty' : '';

			// Ajout de l'élément Poubelle à notre liste
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
					// Lorsque l'on relache un élément sur la poubelle
					drop: function(event, ui){
						// On retire la classe "hover" associée au div .trash
						$(this).removeClass("hover");
						// On ajoute la classe "deleted" au div .trash pour signifier que l'élément a bien été supprimé
						$(this).addClass("deleted");
						// On affiche un petit message "Cet élément a été supprimé" en récupérant la valeur textuelle de l'élément relaché
						$(this).text(ui.draggable.find(".item").text()+" removed !");
						// On supprimer l'élément de la page, le setTimeout est un fix pour IE (http://dev.jqueryui.com/ticket/4088)
						setTimeout(function() { ui.draggable.remove(); }, 1);
						
						// On retourne à l'état originel de la poubelle après 2000 ms soit 2 secondes
						elt = $(this);
						setTimeout(function(){ elt.removeClass("deleted"); elt.html("<i class='fa fa-trash'></i>&nbsp;" + translationObject.trash); }, 2000);
					},
					// Lorsque l'on passe un élément au dessus de la poubelle
					over: function(event, ui){
						// On ajoute la classe "hover" au div .trash
						$(this).addClass("hover");
						// On cache l'élément déplacé
						ui.draggable.hide();
						// On indique via un petit message si l'on veut bien supprimer cet élément
						$(this).text("Remove "+ui.draggable.find(".item").text());
						// On change le curseur
						$(this).css("cursor", "pointer");
					},
					// Lorsque l'on quitte la poubelle
					out: function(event, ui){
						// On retire la classe "hover" au div .trash
						$(this).removeClass("hover");
						// On réaffiche l'élément déplacé
						ui.draggable.show();
						// On remet le texte par défaut
						$(this).text(translationObject.trash);
						// Ainsi que le curseur par défaut
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

					// On ajoute un nouvel item à notre liste
					$(obj).append('<li data-url="' + $("#addUrl").val()+ '" data-newtab="' + $("#newtab").is(':checked')+  '">' + $("#addValue").val() + '</li>');
					// On réinitialise le champ de texte pour l'ajout
					$("#addValue").val("");

					if ( options['link'] )
						$("#addUrl").val("");

					if ( options['newtab'] )
						$("#newtab").attr("checked", false);

					// On ajoute les contrôles à notre nouvel item
					addControls($(obj).find("li:last-child"));

					// Trigger update
					var tabListTitles = fGetListTitles();
					$('.sortableList ul').trigger( "update", tabListTitles );		
				}
			})
			// On autorise également la validation de la saisie d'un nouvel item par pression de la touche entrée
			$("#addValue, #addUrl").live("keyup", function(e) {
				if(e.keyCode == 13) {
					// On lance l'évènement click associé au bouton d'ajout d'item
					$("#addBtn").trigger("click");
				}
			});
			
			// Pour chaque élément trouvé dans la liste de départ
			$(obj).find("li").each(function(){
				// On ajoute les contrôles
				addControls($(this));
			});

		});
				
		/*
		* Fonction qui ajoute les contrôles aux items
		* @Paramètres
		*  - elt: élément courant (liste courante)
		*
		* @Return void
		*/
		
		function addControls(elt)
		{
			var textItem = $(elt).text();

			// On ajoute en premier l'élément textuel
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

			// Puis le champ à enregistrer

			$(elt).append('<input type="hidden" class="sortableField title" name="sortableField[' + slug(textItem) + '][title]" value="' + textItem.replace(/"/g, '&quot;') + '" />');
			
			if ( options['link'] )
				$(elt).append('<input type="hidden" class="sortableField url" name="sortableField[' + slug(textItem) + '][url]" value="' + $(elt).data('url') + '" />');
			
			if ( options['newtab'] )
				$(elt).append('<input type="hidden" class="sortableField newtab" name="sortableField[' + slug(textItem) + '][newtab]" value="' + $(elt).data('newtab') + '" />');
           
			// Puis l'élément de position
			// $(elt).prepend('<span class="count">'+parseInt($(elt).index()+1)+'</span>');

			if ( options['remove'] )
				$(elt).prepend('<span class="remove"><i class="fa fa-remove"></i></span>');

			if ( options['checkbox'] )
				$(elt).prepend('<input type="checkbox" class="check unchecked"/>');
			
			// Au clic sur cet élément
			$(elt).find(".check").click(function(){
				// On alterne la classe de l'item (le <li>), le CSS associé fera que l'élément sera barré
				$(this).parent().toggleClass("bought");
				
				// Si cet élément est acheté
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
				// On récupère sa valeur
				txt = $(this).text().replace(/"/g, '&quot;');
				// url = $(this).parent().data('url');

				// On ajoute un champ de saisie avec la valeur
				$(this).html('<input value="'+txt+'" size="40" />');
				// $(this).append("<input value='"+url+"' />");

				// On la sélectionne par défaut
				$(this).find("input").select();
			})

			// Au double clic sur le lien
			$(elt).find(".url").dblclick(function(){
				// On récupère sa valeur
				url = $(this).parent().attr('data-url');

				// On ajoute un champ de saisie avec la valeur
				$(this).html("<input value='"+url+"' size='40' />");
 
				// On la sélectionne par défaut
				$(this).find("input").select();
			})
			
			// Lorsque l'on quitte la zone de saisie du texte
			$(elt).find(".item input").live("blur", function(){
				// On récupère la valeur du champ de saisie
				txt = $(this).val();
				// On insère dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().find('.sortableField.title').val(txt);				
				$(this).parent().html(txt + "<i class='edit fa fa-pencil'></i>");

				// Trigger update
				var tabListTitles = fGetListTitles();
				$('.sortableList ul').trigger( "update", tabListTitles );		
			})

			// Lorsque l'on quitte la zone de saisie du lien
			$(elt).find(".url input").live("blur", function(){
				// On récupère la valeur du champ de saisie
				url = $(this).val();

				// On insère dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().attr("data-url", url);
				$(this).parent().parent().find('.sortableField.url').val(url);				
				$(this).parent().html(url + "<i class='edit fa fa-pencil'></i>");
				
			});

			// When clicking on checkbox "Open in a new tab"
			$(elt).find(".newtab .edit").on("click", function(){
				// On récupère la valeur du champ de saisie
				newtab = $(this).is(':checked');

				// On insère dans le <li> la nouvelle valeur textuelle
				$(this).parent().parent().attr("data-newtab", newtab);
				$(this).parent().parent().find('.sortableField.newtab').val(newtab);
			})
			
			// On autorise la même action lorsque l'on valide par la touche entrée
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