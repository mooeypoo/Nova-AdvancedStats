$( document ).ready( function () {
	var ctx = $('#ext-advancedStats-chart'),
		settingsPageContent = null,
		$container = $( '#ext-advancedStats-container' ),
		$settings = $( '#advancedStats-settings' ),
		$dates = $( '.ext-advancedStats-title-dates' ),
		url = $container.data( 'url' ),
		recurrence = $container.data( 'recurrence' ),
		dateFormat = function () {
			var format = 'd-M-yy';
			switch ( recurrence ) {
				case 'monthly':
					format = 'M-yy';
					break;
				case 'yearly':
					format = 'yy';
					break;
			}
			return format;
		}(),
		date = {
			start: $container.data( 'date-start' ),
			end: $container.data( 'date-end' )
		},
		$startDate = $( '#ext-advancedStats-date-start' ).datepicker( {
			dateFormat: dateFormat,
			beforeShow: function () {
				var $datepicker = $('#ui-datepicker-div');
				if ( !$datepicker.parent().hasClass( 'ext-advancedStats-datepicker' ) ) {
					$('#ui-datepicker-div').wrap(
						$('<div>').addClass( 'ext-advancedStats-datepicker' )
					);
				}
			}
		} ),
		$endDate = $( '#ext-advancedStats-date-end' ).datepicker( {
			dateFormat: dateFormat,
			beforeShow: function () {
				var $datepicker = $('#ui-datepicker-div');
				if ( !$datepicker.parent().hasClass( 'ext-advancedStats-datepicker' ) ) {
					$('#ui-datepicker-div').wrap(
						$('<div>').addClass( 'ext-advancedStats-datepicker' )
					);
				}
			}
		} ),
		chart = new Chart(ctx, {
			type: 'line',
			legend: {
				display: true,
			},
			data: [],
			options: {
				responsive: true,
				tooltips: {
					position: 'nearest',
					mode: 'index',
					intersect: false
				},
				scales: {
					xAxes: [{
						type: 'time',
						// distribution: 'linear',
						time: {
							unit: 'day'
						}
					}],
					yAxes: [ {
						ticks: {
							min: 0
						}
					}]
				}
			}
		}),
		colors = [ '#007bff', '#f200ff', '#e82c2c', '#259e0f', '#e09b06' ],
		getRandomArrItem = function ( arr, notValues ) {
			var val,
				sanityCounter = 0;
			notValues = notValues || [];

			do {
				val = arr[ Math.floor( Math.random() * arr.length ) ];
				sanityCounter++;
			} while (
				notValues.indexOf( val ) > -1 &&
				sanityCounter < 50
			);

			return val;
		},
		updateChart = function ( data ) {
			var datasets = [],
				chosenColors = [];

			data = Array.isArray( data ) ? data : [ data ];

			data.forEach( function ( dataset ) {
				var color = dataset.color || getRandomArrItem( colors, chosenColors );
				chosenColors.push( color );

				datasets.push( {
					label: dataset.label,
					data: dataset.data,
					lineTension: 0,
					backgroundColor: 'transparent',
					borderColor: color,
					pointBackgroundColor: color
				} );
			} )

			// Update the chart
			chart.config.data = { datasets: datasets };
			chart.update();
		},
		update = function () {
			var start = encodeURIComponent( $startDate.val() ),
				end = encodeURIComponent( $endDate.val() );

			$dates.text( $startDate.val() + ' to ' + $endDate.val() );

			// Graph
			$.getJSON( url + '/Results/combined/' + start + '/' + end )
				.then(
					function ( results ) {
						updateChart( results );
						$container.toggle( true );
					},
					function () { // Failure
						$container.toggle( false );
					}
				);
			// Graph
			$.getJSON( url + '/Results/userstats/' + start + '/' + end )
				.then(
					function ( results ) {
						$( '.ext-advancedStats-userstats-count' ).each( function () {
							var uid = $( this ).data( 'userid' ),
								type = $( this ).data( 'type' ),
								value = results.users[uid].counts[type];

							$( this ).text( value );
						} );
					},
					function () { // Failure
						console.log( 'err', arguments );
						$container.toggle( false );
					}
				);
		};

	// Initialization
	$container.toggle( false );
	$( '#tabs' ).tabs();
	$( 'table.zebra tbody > tr:nth-child(odd)' ).addClass('alt');
	$( '.ext-advancedStats-userstat-table' ).stupidtable();
	$( '.ext-advancedStats-charstats-table' ).stupidtable();
	// Events
	$startDate.on( 'change', update );
	$endDate.on( 'change', update );
	$settings.on( 'click', function () {
		$.get( url + '/Ajax/settings' )
			.then( function ( data ) {
				$.facebox(data);
			} );
	} );

	// Initial values
	update();
} );
