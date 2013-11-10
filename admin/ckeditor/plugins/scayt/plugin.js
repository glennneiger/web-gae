  1 ?/*
  2 Copyright (c) 2003-2011, CKSource - Frederico Knabben. All rights reserved.
  3 For licensing, see LICENSE.html or http://ckeditor.com/license
  4 */
  5 
  6 /**
  7  * @fileOverview Spell Check As You Type (SCAYT).
  8  * Button name : Scayt.
  9  */
 10 
 11 (function()
 12 {
 13 	var commandName  = 'scaytcheck',
 14 		openPage = '';
 15 
 16 	// Checks if a value exists in an array
 17 	function in_array( needle, haystack )
 18 	{
 19 		var found = 0,
 20 			key;
 21 		for ( key in haystack )
 22 		{
 23 			if ( haystack[ key ] == needle )
 24 			{
 25 				found = 1;
 26 				break;
 27 			}
 28 		}
 29 		return found;
 30 	}
 31 
 32 	var onEngineLoad = function()
 33 	{
 34 		var editor = this;
 35 
 36 		var createInstance = function()	// Create new instance every time Document is created.
 37 		{
 38 			var config = editor.config;
 39 			// Initialise Scayt instance.
 40 			var oParams = {};
 41 			// Get the iframe.
 42 			oParams.srcNodeRef = editor.document.getWindow().$.frameElement;
 43 			// syntax : AppName.AppVersion@AppRevision
 44 			oParams.assocApp  = 'CKEDITOR.' + CKEDITOR.version + '@' + CKEDITOR.revision;
 45 			oParams.customerid = config.scayt_customerid  || '1:WvF0D4-UtPqN1-43nkD4-NKvUm2-daQqk3-LmNiI-z7Ysb4-mwry24-T8YrS3-Q2tpq2';
 46 			oParams.customDictionaryIds = config.scayt_customDictionaryIds || '';
 47 			oParams.userDictionaryName = config.scayt_userDictionaryName || '';
 48 			oParams.sLang = config.scayt_sLang || 'en_US';
 49 
 50 			// Introduce SCAYT onLoad callback. (#5632)
 51 			oParams.onLoad = function()
 52 				{
 53 					// Draw down word marker to avoid being covered by background-color style.(#5466)
 54 					if ( !( CKEDITOR.env.ie && CKEDITOR.env.version < 8 ) )
 55 						this.addStyle( this.selectorCss(), 'padding-bottom: 2px !important;' );
 56 
 57 					// Call scayt_control.focus when SCAYT loaded
 58 					// and only if editor has focus and scayt control creates at first time (#5720)
 59 					if ( editor.focusManager.hasFocus && !plugin.isControlRestored( editor ) )
 60 						this.focus();
 61 
 62 				};
 63 
 64 			oParams.onBeforeChange = function()
 65 			{
 66 				if ( plugin.getScayt( editor ) && !editor.checkDirty() )
 67 					setTimeout( function(){ editor.resetDirty(); }, 0 );
 68 			};
 69 
 70 			var scayt_custom_params = window.scayt_custom_params;
 71 			if ( typeof scayt_custom_params == 'object' )
 72 			{
 73 				for ( var k in scayt_custom_params )
 74 					oParams[ k ] = scayt_custom_params[ k ];
 75 			}
 76 			// needs for restoring a specific scayt control settings
 77 			if ( plugin.getControlId( editor ) )
 78 				oParams.id = plugin.getControlId( editor );
 79 
 80 			var scayt_control = new window.scayt( oParams );
 81 
 82 			scayt_control.afterMarkupRemove.push( function( node )
 83 			{
 84 				( new CKEDITOR.dom.element( node, scayt_control.document ) ).mergeSiblings();
 85 			} );
 86 
 87 			// Copy config.
 88 			var lastInstance = plugin.instances[ editor.name ];
 89 			if ( lastInstance )
 90 			{
 91 				scayt_control.sLang = lastInstance.sLang;
 92 				scayt_control.option( lastInstance.option() );
 93 				scayt_control.paused = lastInstance.paused;
 94 			}
 95 
 96 			plugin.instances[ editor.name ] = scayt_control;
 97 
 98 			try {
 99 				scayt_control.setDisabled( plugin.isPaused( editor ) === false );
100 			} catch (e) {}
101 
102 			editor.fire( 'showScaytState' );
103 		};
104 
105 		editor.on( 'contentDom', createInstance );
106 		editor.on( 'contentDomUnload', function()
107 			{
108 				// Remove scripts.
109 				var scripts = CKEDITOR.document.getElementsByTag( 'script' ),
110 					scaytIdRegex =  /^dojoIoScript(\d+)$/i,
111 					scaytSrcRegex =  /^https?:\/\/svc\.webspellchecker\.net\/spellcheck\/script\/ssrv\.cgi/i;
112 
113 				for ( var i=0; i < scripts.count(); i++ )
114 				{
115 					var script = scripts.getItem( i ),
116 						id = script.getId(),
117 						src = script.getAttribute( 'src' );
118 
119 					if ( id && src && id.match( scaytIdRegex ) && src.match( scaytSrcRegex ))
120 						script.remove();
121 				}
122 			});
123 
124 		editor.on( 'beforeCommandExec', function( ev )		// Disable SCAYT before Source command execution.
125 			{
126 				if ( ( ev.data.name == 'source' || ev.data.name == 'newpage' ) && editor.mode == 'wysiwyg' )
127 				{
128 					var scayt_instance = plugin.getScayt( editor );
129 					if ( scayt_instance )
130 					{
131 						plugin.setPaused( editor, !scayt_instance.disabled );
132 						// store a control id for restore a specific scayt control settings
133 						plugin.setControlId( editor, scayt_instance.id );
134 						scayt_instance.destroy( true );
135 						delete plugin.instances[ editor.name ];
136 					}
137 				}
138 				// Catch on source mode switch off (#5720)
139 				else if ( ev.data.name == 'source'  && editor.mode == 'source' )
140 					plugin.markControlRestore( editor );
141 			});
142 
143 		editor.on( 'afterCommandExec', function( ev )
144 			{
145 				if ( !plugin.isScaytEnabled( editor ) )
146 					return;
147 
148 				if ( editor.mode == 'wysiwyg' && ( ev.data.name == 'undo' || ev.data.name == 'redo' ) )
149 					window.setTimeout( function() { plugin.getScayt( editor ).refresh(); }, 10 );
150 			});
151 
152 		editor.on( 'destroy', function( ev )
153 			{
154 				var editor = ev.editor,
155 					scayt_instance = plugin.getScayt( editor );
156 
157 				// SCAYT instance might already get destroyed by mode switch (#5744).
158 				if ( !scayt_instance )
159 					return;
160 
161 				delete plugin.instances[ editor.name ];
162 				// store a control id for restore a specific scayt control settings
163 				plugin.setControlId( editor, scayt_instance.id );
164 				scayt_instance.destroy( true );
165 			});
166 
167 		// Listen to data manipulation to reflect scayt markup.
168 		editor.on( 'afterSetData', function()
169 			{
170 				if ( plugin.isScaytEnabled( editor ) ) {
171 					window.setTimeout( function()
172 						{
173 							var instance = plugin.getScayt( editor );
174 							instance && instance.refresh();
175 						}, 10 );
176 				}
177 			});
178 
179 		// Reload spell-checking for current word after insertion completed.
180 		editor.on( 'insertElement', function()
181 			{
182 				var scayt_instance = plugin.getScayt( editor );
183 				if ( plugin.isScaytEnabled( editor ) )
184 				{
185 					// Unlock the selection before reload, SCAYT will take
186 					// care selection update.
187 					if ( CKEDITOR.env.ie )
188 						editor.getSelection().unlock( true );
189 
190 					// Return focus to the editor and refresh SCAYT markup (#5573).
191 					window.setTimeout( function()
192 					{
193 						scayt_instance.focus();
194 						scayt_instance.refresh();
195 					}, 10 );
196 				}
197 			}, this, null, 50 );
198 
199 		editor.on( 'insertHtml', function()
200 			{
201 				var scayt_instance = plugin.getScayt( editor );
202 				if ( plugin.isScaytEnabled( editor ) )
203 				{
204 					// Unlock the selection before reload, SCAYT will take
205 					// care selection update.
206 					if ( CKEDITOR.env.ie )
207 						editor.getSelection().unlock( true );
208 
209 					// Return focus to the editor (#5573)
210 					// Refresh SCAYT markup
211 					window.setTimeout( function()
212 					{
213 						scayt_instance.focus();
214 						scayt_instance.refresh();
215 					}, 10 );
216 				}
217 			}, this, null, 50 );
218 
219 		editor.on( 'scaytDialog', function( ev )	// Communication with dialog.
220 			{
221 				ev.data.djConfig = window.djConfig;
222 				ev.data.scayt_control = plugin.getScayt( editor );
223 				ev.data.tab = openPage;
224 				ev.data.scayt = window.scayt;
225 			});
226 
227 		var dataProcessor = editor.dataProcessor,
228 			htmlFilter = dataProcessor && dataProcessor.htmlFilter;
229 
230 		if ( htmlFilter )
231 		{
232 			htmlFilter.addRules(
233 				{
234 					elements :
235 					{
236 						span : function( element )
237 						{
238 							if ( element.attributes[ 'data-scayt_word' ]
239 									&& element.attributes[ 'data-scaytid' ] )
240 							{
241 								delete element.name;	// Write children, but don't write this node.
242 								return element;
243 							}
244 						}
245 					}
246 				}
247 			);
248 		}
249 
250 		// Override Image.equals method avoid CK snapshot module to add SCAYT markup to snapshots. (#5546)
251 		var undoImagePrototype = CKEDITOR.plugins.undo.Image.prototype;
252 		undoImagePrototype.equals = CKEDITOR.tools.override( undoImagePrototype.equals, function( org )
253 		{
254 			return function( otherImage )
255 			{
256 				var thisContents = this.contents,
257 					otherContents = otherImage.contents;
258 				var scayt_instance = plugin.getScayt( this.editor );
259 				// Making the comparison based on content without SCAYT word markers.
260 				if ( scayt_instance && plugin.isScaytReady( this.editor ) )
261 				{
262 					// scayt::reset might return value undefined. (#5742)
263 					this.contents = scayt_instance.reset( thisContents ) || '';
264 					otherImage.contents = scayt_instance.reset( otherContents ) || '';
265 				}
266 
267 				var retval = org.apply( this, arguments );
268 
269 				this.contents = thisContents;
270 				otherImage.contents = otherContents;
271 				return retval;
272 			};
273 		});
274 
275 		if ( editor.document )
276 			createInstance();
277 	};
278 
279 CKEDITOR.plugins.scayt =
280 	{
281 		engineLoaded : false,
282 		instances : {},
283 		// Data storage for SCAYT control, based on editor instances
284 		controlInfo : {},
285 		setControlInfo : function( editor, o )
286 		{
287 			if ( editor && editor.name && typeof ( this.controlInfo[ editor.name ] ) != 'object' )
288 				this.controlInfo[ editor.name ] = {};
289 
290 			for ( var infoOpt in o )
291 				this.controlInfo[ editor.name ][ infoOpt ] = o[ infoOpt ];
292 		},
293 		isControlRestored : function( editor )
294 		{
295 			if ( editor &&
296 					editor.name &&
297 					this.controlInfo[ editor.name ] )
298 			{
299 				return this.controlInfo[ editor.name ].restored ;
300 			}
301 			return false;
302 		},
303 		markControlRestore : function( editor )
304 		{
305 			this.setControlInfo( editor, { restored:true } );
306 		},
307 		setControlId: function( editor, id )
308 		{
309 			this.setControlInfo( editor, { id:id } );
310 		},
311 		getControlId: function( editor )
312 		{
313 			if ( editor &&
314 					editor.name &&
315 					this.controlInfo[ editor.name ] &&
316 					this.controlInfo[ editor.name ].id )
317 			{
318 				return this.controlInfo[ editor.name ].id;
319 			}
320 			return null;
321 		},
322 		setPaused: function( editor , bool )
323 		{
324 			this.setControlInfo( editor, { paused:bool } );
325 		},
326 		isPaused: function( editor )
327 		{
328 			if ( editor &&
329 					editor.name &&
330 					this.controlInfo[editor.name] )
331 			{
332 				return this.controlInfo[editor.name].paused;
333 			}
334 			return undefined;
335 		},
336 		getScayt : function( editor )
337 		{
338 			return this.instances[ editor.name ];
339 		},
340 		isScaytReady : function( editor )
341 		{
342 			return this.engineLoaded === true &&
343 				'undefined' !== typeof window.scayt && this.getScayt( editor );
344 		},
345 		isScaytEnabled : function( editor )
346 		{
347 			var scayt_instance = this.getScayt( editor );
348 			return ( scayt_instance ) ? scayt_instance.disabled === false : false;
349 		},
350 		getUiTabs : function( editor )
351 		{
352 			var uiTabs = [];
353 
354 			// read UI tabs value from config
355 			var configUiTabs = editor.config.scayt_uiTabs || "1,1,1";
356 
357 			// convert string to array
358 			configUiTabs = configUiTabs.split( ',' );
359 
360 			// "About us" should be always shown for standard config
361 			configUiTabs[3] = "1";
362 
363 			for ( var i = 0; i < 4; i++ ) {
364 				uiTabs[i] = (typeof window.scayt != "undefined" && typeof window.scayt.uiTags != "undefined")
365 								? (parseInt(configUiTabs[i],10) && window.scayt.uiTags[i])
366 								: parseInt(configUiTabs[i],10);
367 			}
368 			return uiTabs;
369 		},
370 		loadEngine : function( editor )
371 		{
372 			// SCAYT doesn't work with Firefox2, Opera and AIR.
373 			if ( CKEDITOR.env.gecko && CKEDITOR.env.version < 10900 || CKEDITOR.env.opera || CKEDITOR.env.air )
374 				return editor.fire( 'showScaytState' );
375 
376 			if ( this.engineLoaded === true )
377 				return onEngineLoad.apply( editor );	// Add new instance.
378 			else if ( this.engineLoaded == -1 )			// We are waiting.
379 				return CKEDITOR.on( 'scaytReady', function(){ onEngineLoad.apply( editor ); } );	// Use function(){} to avoid rejection as duplicate.
380 
381 			CKEDITOR.on( 'scaytReady', onEngineLoad, editor );
382 			CKEDITOR.on( 'scaytReady', function()
383 				{
384 					this.engineLoaded = true;
385 				},
386 				this,
387 				null,
388 				0
389 			);	// First to run.
390 
391 			this.engineLoaded = -1;	// Loading in progress.
392 
393 			// compose scayt url
394 			var protocol = document.location.protocol;
395 			// Default to 'http' for unknown.
396 			protocol = protocol.search( /https?:/) != -1? protocol : 'http:';
397 			var baseUrl  = 'svc.webspellchecker.net/scayt26/loader__base.js';
398 
399 			var scaytUrl  =  editor.config.scayt_srcUrl || ( protocol + '//' + baseUrl );
400 			var scaytConfigBaseUrl =  plugin.parseUrl( scaytUrl ).path +  '/';
401 
402 			if( window.scayt == undefined )
403 			{
404 				CKEDITOR._djScaytConfig =
405 				{
406 					baseUrl: scaytConfigBaseUrl,
407 					addOnLoad:
408 					[
409 						function()
410 						{
411 							CKEDITOR.fireOnce( 'scaytReady' );
412 						}
413 					],
414 					isDebug: false
415 				};
416 				// Append javascript code.
417 				CKEDITOR.document.getHead().append(
418 					CKEDITOR.document.createElement( 'script',
419 						{
420 							attributes :
421 								{
422 									type : 'text/javascript',
423 									async : 'true',
424 									src : scaytUrl
425 								}
426 						})
427 				);
428 			}
429 			else
430 				CKEDITOR.fireOnce( 'scaytReady' );
431 
432 			return null;
433 		},
434 		parseUrl : function ( data )
435 		{
436 			var match;
437 			if ( data.match && ( match = data.match(/(.*)[\/\\](.*?\.\w+)$/) ) )
438 				return { path: match[1], file: match[2] };
439 			else
440 				return data;
441 		}
442 	};
443 
444 	var plugin = CKEDITOR.plugins.scayt;
445 
446 	// Context menu constructing.
447 	var addButtonCommand = function( editor, buttonName, buttonLabel, commandName, command, menugroup, menuOrder )
448 	{
449 		editor.addCommand( commandName, command );
450 
451 		// If the "menu" plugin is loaded, register the menu item.
452 		editor.addMenuItem( commandName,
453 			{
454 				label : buttonLabel,
455 				command : commandName,
456 				group : menugroup,
457 				order : menuOrder
458 			});
459 	};
460 
461 	var commandDefinition =
462 	{
463 		preserveState : true,
464 		editorFocus : false,
465 		canUndo : false,
466 
467 		exec: function( editor )
468 		{
469 			if ( plugin.isScaytReady( editor ) )
470 			{
471 				var isEnabled = plugin.isScaytEnabled( editor );
472 
473 				this.setState( isEnabled ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_ON );
474 
475 				var scayt_control = plugin.getScayt( editor );
476 				// the place where the status of editor focus should be restored
477 				// after there will be ability to store its state before SCAYT button click
478 				// if (storedFocusState is focused )
479 				//   scayt_control.focus();
480 				//
481 				// now focus is set certainly
482 				scayt_control.focus();
483 				scayt_control.setDisabled( isEnabled );
484 			}
485 			else if ( !editor.config.scayt_autoStartup && plugin.engineLoaded >= 0 )	// Load first time
486 			{
487 				this.setState( CKEDITOR.TRISTATE_DISABLED );
488 				plugin.loadEngine( editor );
489 			}
490 		}
491 	};
492 
493 	// Add scayt plugin.
494 	CKEDITOR.plugins.add( 'scayt',
495 	{
496 		requires : [ 'menubutton' ],
497 
498 		beforeInit : function( editor )
499 		{
500 			var items_order = editor.config.scayt_contextMenuItemsOrder
501 					|| 'suggest|moresuggest|control',
502 				items_order_str = "";
503 
504 			items_order = items_order.split( '|' );
505 
506 			if ( items_order && items_order.length )
507 			{
508 				for ( var pos = 0 ; pos < items_order.length ; pos++ )
509 					items_order_str += 'scayt_' + items_order[ pos ] + ( items_order.length != parseInt( pos, 10 ) + 1 ? ',' : '' );
510 			}
511 
512 			// Put it on top of all context menu items (#5717)
513 			editor.config.menu_groups =  items_order_str + ',' + editor.config.menu_groups;
514 		},
515 
516 		init : function( editor )
517 		{
518 			// Delete span[data-scaytid] when text pasting in editor (#6921)
519 			var dataFilter = editor.dataProcessor && editor.dataProcessor.dataFilter;
520 			var dataFilterRules =
521 			{
522 					elements :
523 					{
524 							span : function( element )
525 							{
526 									var attrs = element.attributes;
527 									if ( attrs && attrs[ 'data-scaytid' ] )
528 											delete element.name;
529 							}
530 					}
531 			};
532 			dataFilter && dataFilter.addRules( dataFilterRules );
533 
534 			var moreSuggestions = {},
535 				mainSuggestions = {};
536 
537 			// Scayt command.
538 			var command = editor.addCommand( commandName, commandDefinition );
539 
540 			// Add Options dialog.
541 			CKEDITOR.dialog.add( commandName, CKEDITOR.getUrl( this.path + 'dialogs/options.js' ) );
542 
543 			var uiTabs = plugin.getUiTabs( editor );
544 
545 			var menuGroup = 'scaytButton';
546 			editor.addMenuGroup( menuGroup );
547 			// combine menu items to render
548 			var uiMenuItems = {};
549 
550 			var lang = editor.lang.scayt;
551 
552 			// always added
553 			uiMenuItems.scaytToggle =
554 				{
555 					label : lang.enable,
556 					command : commandName,
557 					group : menuGroup
558 				};
559 
560 			if ( uiTabs[0] == 1 )
561 				uiMenuItems.scaytOptions =
562 				{
563 					label : lang.options,
564 					group : menuGroup,
565 					onClick : function()
566 					{
567 						openPage = 'options';
568 						editor.openDialog( commandName );
569 					}
570 				};
571 
572 			if ( uiTabs[1] == 1 )
573 				uiMenuItems.scaytLangs =
574 				{
575 					label : lang.langs,
576 					group : menuGroup,
577 					onClick : function()
578 					{
579 						openPage = 'langs';
580 						editor.openDialog( commandName );
581 					}
582 				};
583 			if ( uiTabs[2] == 1 )
584 				uiMenuItems.scaytDict =
585 				{
586 					label : lang.dictionariesTab,
587 					group : menuGroup,
588 					onClick : function()
589 					{
590 						openPage = 'dictionaries';
591 						editor.openDialog( commandName );
592 					}
593 				};
594 			// always added
595 			uiMenuItems.scaytAbout =
596 				{
597 					label : editor.lang.scayt.about,
598 					group : menuGroup,
599 					onClick : function()
600 					{
601 						openPage = 'about';
602 						editor.openDialog( commandName );
603 					}
604 				};
605 
606 			editor.addMenuItems( uiMenuItems );
607 
608 				editor.ui.add( 'Scayt', CKEDITOR.UI_MENUBUTTON,
609 					{
610 						label : lang.title,
611 						title : CKEDITOR.env.opera ? lang.opera_title : lang.title,
612 						className : 'cke_button_scayt',
613 						modes : { wysiwyg : 1 },
614 						onRender: function()
615 						{
616 							command.on( 'state', function()
617 							{
618 								this.setState( command.state );
619 							},
620 							this);
621 						},
622 						onMenu : function()
623 						{
624 							var isEnabled = plugin.isScaytEnabled( editor );
625 
626 							editor.getMenuItem( 'scaytToggle' ).label = lang[ isEnabled ? 'disable' : 'enable' ];
627 
628 							var uiTabs = plugin.getUiTabs( editor );
629 
630 							return {
631 								scaytToggle  : CKEDITOR.TRISTATE_OFF,
632 								scaytOptions : isEnabled && uiTabs[0] ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED,
633 								scaytLangs   : isEnabled && uiTabs[1] ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED,
634 								scaytDict    : isEnabled && uiTabs[2] ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED,
635 								scaytAbout   : isEnabled && uiTabs[3] ? CKEDITOR.TRISTATE_OFF : CKEDITOR.TRISTATE_DISABLED
636 							};
637 						}
638 					});
639 
640 			// If the "contextmenu" plugin is loaded, register the listeners.
641 			if ( editor.contextMenu && editor.addMenuItems )
642 			{
643 				editor.contextMenu.addListener( function( element, selection )
644 					{
645 						if ( !plugin.isScaytEnabled( editor )
646 								|| selection.getRanges()[ 0 ].checkReadOnly() )
647 							return null;
648 
649 						var scayt_control = plugin.getScayt( editor ),
650 							node = scayt_control.getScaytNode();
651 
652 						if ( !node )
653 							return null;
654 
655 							var word = scayt_control.getWord( node );
656 
657 						if ( !word )
658 							return null;
659 
660 						var sLang = scayt_control.getLang(),
661 							_r = {},
662 							items_suggestion = window.scayt.getSuggestion( word, sLang );
663 						if ( !items_suggestion || !items_suggestion.length )
664 							return null;
665 						// Remove unused commands and menuitems
666 						for ( var m in moreSuggestions )
667 						{
668 							delete editor._.menuItems[ m ];
669 							delete editor._.commands[ m ];
670 						}
671 						for ( m in mainSuggestions )
672 						{
673 							delete editor._.menuItems[ m ];
674 							delete editor._.commands[ m ];
675 						}
676 						moreSuggestions = {};		// Reset items.
677 						mainSuggestions = {};
678 
679 						var moreSuggestionsUnable = editor.config.scayt_moreSuggestions || 'on';
680 						var moreSuggestionsUnableAdded = false;
681 
682 						var maxSuggestions = editor.config.scayt_maxSuggestions;
683 						( typeof maxSuggestions != 'number' ) && ( maxSuggestions = 5 );
684 						!maxSuggestions && ( maxSuggestions = items_suggestion.length );
685 
686 						var contextCommands = editor.config.scayt_contextCommands || 'all';
687 						contextCommands = contextCommands.split( '|' );
688 
689 						for ( var i = 0, l = items_suggestion.length; i < l; i += 1 )
690 						{
691 							var commandName = 'scayt_suggestion_' + items_suggestion[i].replace( ' ', '_' );
692 							var exec = ( function( el, s )
693 								{
694 									return {
695 										exec: function()
696 										{
697 											scayt_control.replace( el, s );
698 										}
699 									};
700 								})( node, items_suggestion[i] );
701 
702 							if ( i < maxSuggestions )
703 							{
704 								addButtonCommand( editor, 'button_' + commandName, items_suggestion[i],
705 									commandName, exec, 'scayt_suggest', i + 1 );
706 								_r[ commandName ] = CKEDITOR.TRISTATE_OFF;
707 								mainSuggestions[ commandName ] = CKEDITOR.TRISTATE_OFF;
708 							}
709 							else if ( moreSuggestionsUnable == 'on' )
710 							{
711 								addButtonCommand( editor, 'button_' + commandName, items_suggestion[i],
712 									commandName, exec, 'scayt_moresuggest', i + 1 );
713 								moreSuggestions[ commandName ] = CKEDITOR.TRISTATE_OFF;
714 								moreSuggestionsUnableAdded = true;
715 							}
716 						}
717 
718 						if ( moreSuggestionsUnableAdded )
719 						{
720 							// Register the More suggestions group;
721 							editor.addMenuItem( 'scayt_moresuggest',
722 							{
723 								label : lang.moreSuggestions,
724 								group : 'scayt_moresuggest',
725 								order : 10,
726 								getItems : function()
727 								{
728 									return moreSuggestions;
729 								}
730 							});
731 							mainSuggestions[ 'scayt_moresuggest' ] = CKEDITOR.TRISTATE_OFF;
732 						}
733 
734 						if ( in_array( 'all', contextCommands )  || in_array( 'ignore', contextCommands)  )
735 						{
736 							var ignore_command = {
737 								exec: function(){
738 									scayt_control.ignore( node );
739 								}
740 							};
741 							addButtonCommand( editor, 'ignore', lang.ignore, 'scayt_ignore', ignore_command, 'scayt_control', 1 );
742 							mainSuggestions[ 'scayt_ignore' ] = CKEDITOR.TRISTATE_OFF;
743 						}
744 
745 						if ( in_array( 'all', contextCommands )  || in_array( 'ignoreall', contextCommands ) )
746 						{
747 							var ignore_all_command = {
748 								exec: function(){
749 									scayt_control.ignoreAll( node );
750 								}
751 							};
752 							addButtonCommand(editor, 'ignore_all', lang.ignoreAll, 'scayt_ignore_all', ignore_all_command, 'scayt_control', 2);
753 							mainSuggestions['scayt_ignore_all'] = CKEDITOR.TRISTATE_OFF;
754 						}
755 
756 						if ( in_array( 'all', contextCommands )  || in_array( 'add', contextCommands ) )
757 						{
758 							var addword_command = {
759 								exec: function(){
760 									window.scayt.addWordToUserDictionary( node );
761 								}
762 							};
763 							addButtonCommand(editor, 'add_word', lang.addWord, 'scayt_add_word', addword_command, 'scayt_control', 3);
764 							mainSuggestions['scayt_add_word'] = CKEDITOR.TRISTATE_OFF;
765 						}
766 
767 						if ( scayt_control.fireOnContextMenu )
768 							scayt_control.fireOnContextMenu( editor );
769 
770 						return mainSuggestions;
771 					});
772 			}
773 
774 			var showInitialState = function()
775 				{
776 					editor.removeListener( 'showScaytState', showInitialState );
777 
778 					if ( !CKEDITOR.env.opera && !CKEDITOR.env.air )
779 						command.setState( plugin.isScaytEnabled( editor ) ? CKEDITOR.TRISTATE_ON : CKEDITOR.TRISTATE_OFF );
780 					else
781 						command.setState( CKEDITOR.TRISTATE_DISABLED );
782 				};
783 
784 			editor.on( 'showScaytState', showInitialState );
785 
786 			if ( CKEDITOR.env.opera || CKEDITOR.env.air )
787 			{
788 				editor.on( 'instanceReady', function()
789 				{
790 					showInitialState();
791 				});
792 			}
793 
794 			// Start plugin
795 			if ( editor.config.scayt_autoStartup )
796 			{
797 				editor.on( 'instanceReady', function()
798 				{
799 					plugin.loadEngine( editor );
800 				});
801 			}
802 		},
803 
804 		afterInit : function( editor )
805 		{
806 			// Prevent word marker line from displaying in elements path and been removed when cleaning format. (#3570) (#4125)
807 			var elementsPathFilters,
808 					scaytFilter = function( element )
809 					{
810 						if ( element.hasAttribute( 'data-scaytid' ) )
811 							return false;
812 					};
813 
814 			if ( editor._.elementsPath && ( elementsPathFilters = editor._.elementsPath.filters ) )
815 				elementsPathFilters.push( scaytFilter );
816 
817 			editor.addRemoveFormatFilter && editor.addRemoveFormatFilter( scaytFilter );
818 
819 		}
820 	});
821 })();
822 
823 /**
824  * If enabled (set to <code>true</code>), turns on SCAYT automatically
825  * after loading the editor.
826  * @name CKEDITOR.config.scayt_autoStartup
827  * @type Boolean
828  * @default <code>false</code>
829  * @example
830  * config.scayt_autoStartup = true;
831  */
832 config.scayt_autoStartup = true;
833 /**
834  * Defines the number of SCAYT suggestions to show in the main context menu.
835  * Possible values are:
836  * <ul>
837  *	<li><code>0</code> (zero) – All suggestions are displayed in the main context menu.</li>
838  *	<li>Positive number – The maximum number of suggestions to show in the context
839  *		menu. Other entries will be shown in the "More Suggestions" sub-menu.</li>
840  *	<li>Negative number – No suggestions are shown in the main context menu. All
841  *		entries will be listed in the the "Suggestions" sub-menu.</li>
842  * </ul>
843  * @name CKEDITOR.config.scayt_maxSuggestions
844  * @type Number
845  * @default <code>5</code>
846  * @example
847  * // Display only three suggestions in the main context menu.
848  * config.scayt_maxSuggestions = 3;
849  * @example
850  * // Do not show the suggestions directly.
851  * config.scayt_maxSuggestions = -1;
852  */
853 
854 /**
855  * Sets the customer ID for SCAYT. Required for migration from free,
856  * ad-supported version to paid, ad-free version.
857  * @name CKEDITOR.config.scayt_customerid
858  * @type String
859  * @default <code>''</code>
860  * @example
861  * // Load SCAYT using my customer ID.
862  * config.scayt_customerid  = 'your-encrypted-customer-id';
863  */
864 
865 /**
866  * Enables/disables the "More Suggestions" sub-menu in the context menu.
867  * Possible values are <code>on</code> and <code>off</code>.
868  * @name CKEDITOR.config.scayt_moreSuggestions
869  * @type String
870  * @default <code>'on'</code>
871  * @example
872  * // Disables the "More Suggestions" sub-menu.
873  * config.scayt_moreSuggestions = 'off';
874  */
875 
876 /**
877  * Customizes the display of SCAYT context menu commands ("Add Word", "Ignore"
878  * and "Ignore All"). This must be a string with one or more of the following
879  * words separated by a pipe character ("|"):
880  * <ul>
881  *	<li><code>off</code> – disables all options.</li>
882  *	<li><code>all</code> – enables all options.</li>
883  *	<li><code>ignore</code> – enables the "Ignore" option.</li>
884  *	<li><code>ignoreall</code> – enables the "Ignore All" option.</li>
885  *	<li><code>add</code> – enables the "Add Word" option.</li>
886  * </ul>
887  * @name CKEDITOR.config.scayt_contextCommands
888  * @type String
889  * @default <code>'all'</code>
890  * @example
891  * // Show only "Add Word" and "Ignore All" in the context menu.
892  * config.scayt_contextCommands = 'add|ignoreall';
893  */
894 
895 /**
896  * Sets the default spell checking language for SCAYT. Possible values are:
897  * <code>en_US</code>, <code>en_GB</code>, <code>pt_BR</code>, <code>da_DK</code>,
898  * <code>nl_NL</code>, <code>en_CA</code>, <code>fi_FI</code>, <code>fr_FR</code>,
899  * <code>fr_CA</code>, <code>de_DE</code>, <code>el_GR</code>, <code>it_IT</code>,
900  * <code>nb_NO</code>, <code>pt_PT</code>, <code>es_ES</code>, <code>sv_SE</code>.
901  * @name CKEDITOR.config.scayt_sLang
902  * @type String
903  * @default <code>'en_US'</code>
904  * @example
905  * // Sets SCAYT to German.
906  * config.scayt_sLang = 'de_DE';
907  */
908 
909 /**
910  * Sets the visibility of particular tabs in the SCAYT dialog window and toolbar
911  * button. This setting must contain a <code>1</code> (enabled) or <code>0</code>
912  * (disabled) value for each of the following entries, in this precise order,
913  * separated by a comma (","): "Options", "Languages", and "Dictionary".
914  * @name CKEDITOR.config.scayt_uiTabs
915  * @type String
916  * @default <code>'1,1,1'</code>
917  * @example
918  * // Hides the "Languages" tab.
919  * config.scayt_uiTabs = '1,0,1';
920  */
921 
922 
923 /**
924  * Sets the URL to SCAYT core. Required to switch to the licensed version of SCAYT application.
925  * Further details available at
926  * <a href="http://wiki.webspellchecker.net/doku.php?id=migration:hosredfreetolicensedck">
927  * http://wiki.webspellchecker.net/doku.php?id=migration:hosredfreetolicensedck</a>.
928  * @name CKEDITOR.config.scayt_srcUrl
929  * @type String
930  * @default <code>''</code>
931  * @example
932  * config.scayt_srcUrl = "http://my-host/spellcheck/lf/scayt/scayt.js";
933  */
934 
935 /**
936  * Links SCAYT to custom dictionaries. This is a string containing dictionary IDs
937  * separared by commas (","). Available only for the licensed version.
938  * Further details at
939  * <a href="http://wiki.webspellchecker.net/doku.php?id=installationandconfiguration:customdictionaries:licensed">
940  * http://wiki.webspellchecker.net/doku.php?id=installationandconfiguration:customdictionaries:licensed</a>.
941  * @name CKEDITOR.config.scayt_customDictionaryIds
942  * @type String
943  * @default <code>''</code>
944  * @example
945  * config.scayt_customDictionaryIds = '3021,3456,3478"';
946  */
947 
948 /**
949  * Makes it possible to activate a custom dictionary in SCAYT. The user
950  * dictionary name must be used. Available only for the licensed version.
951  * @name CKEDITOR.config.scayt_userDictionaryName
952  * @type String
953  * @default <code>''</code>
954  * @example
955  * config.scayt_userDictionaryName = 'MyDictionary';
956  */
957 
958 /**
959  * Defines the order SCAYT context menu items by groups.
960  * This must be a string with one or more of the following
961  * words separated by a pipe character ("|"):
962  * <ul>
963  *     <li><code>suggest</code> – main suggestion word list,</li>
964  *     <li><code>moresuggest</code> – more suggestions word list,</li>
965  *     <li><code>control</code> – SCAYT commands, such as "Ignore" and "Add Word".</li>
966  * </ul>
967  *
968  * @name CKEDITOR.config.scayt_contextMenuItemsOrder
969  * @type String
970  * @default <code>'suggest|moresuggest|control'</code>
971  * @example
972  * config.scayt_contextMenuItemsOrder = 'moresuggest|control|suggest';
973  */
974 