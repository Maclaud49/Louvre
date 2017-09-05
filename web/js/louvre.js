/********************************Booking date Date picker config *******************************************************/

/* create an array of days which need to be disabled */
var disabledDays = ["1-1",// Jour de l'an
                    "17-4",// Lundi de Pâques
                    "1-5",// 1er mai
                    "8-5",// 8 mai 1945
                    "25-5",// Jeudi de l'Ascension
                    "5-6",// Lundi de Pentecôte
                    "14-7",// Fête nationale
                    "15-8",// Assomption
                    "1-11",// La Toussaint
                    "11-11",// L'armistisce
                    "25-12",// Noël
                    ]

/* Disabled selected holidays */
function nationalDays(date) {
    var $m = date.getMonth(), $d = date.getDate();
    for (i = 0; i < disabledDays.length; i++) {
        if($.inArray($d + '-' + ($m+1),disabledDays) != -1) {
            return [false];
        }
    }
    return [true];
}
function noSundayOrTuesdayOrHolidays(date) {
    var $day = date.getDay();
    return ($day!=0)&& ($day!=2)? nationalDays(date) : '';
}

//Once the page is loaded
$(function(){

    //Configuration of date picker for French version
    $.datepicker.regional['fr'] = {
        closeText: 'Fermer',
        prevText: 'Précédent',
        nextText: 'Suivant',
        currentText: 'Aujourd\'hui',
        monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
        monthNamesShort: ['Janv.','Févr.','Mars','Avril','Mai','Juin','Juil.','Août','Sept.','Oct.','Nov.','Déc.'],
        dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
        dayNamesShort: ['Dim.','Lun.','Mar.','Mer.','Jeu.','Ven.','Sam.'],
        dayNamesMin: ['D','L','M','M','J','V','S'],
        weekHeader: 'Sem.',
        dateFormat: 'dd/mm/yy',
        constraintInput:true,
        beforeShowDay: noSundayOrTuesdayOrHolidays,
        minDate:'0',
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''};
    $.datepicker.setDefaults($.datepicker.regional['fr']);




    //datepicker for booking date on read only mode
    $( ".bookingDatepicker").datepicker();
    $('#order_bookingDate').attr("readonly","readonly");

    //booking-date change
    $( ".bookingDatepicker").change(function(){
        var $qty =$('#order_quantity').val();
        if($qty<0){
            $('#ticketsQtyMessage').text('Veuillez choisir une quantité de billet positive');
            $('#ticketsQtyMessage').show(0);
        }
        else{
            $('#ticketsQtyMessage').text('');
            $('#ticketsQtyMessage').hide(0);
            $('#openDaysInformationMessage').hide();
            $('#PricesInformationMessage').show(0);
            $('#order_quantity').removeAttr('disabled');
            $('#order_quantity').removeAttr('title');
            halfDayTicket();
        }
    })

    /******************************************booking page load*****************************************************/

    qtyBtn();
    validateBtn();

    /****************************************************************************************************************/


    //display tickets depending on the qty
    $('#order_quantity').change(function(){
        var $date = $( ".bookingDatepicker").val();
        var $qty =$('#order_quantity').val();
        if($qty<0 && $date==""){
            $('#ticketsQtyMessageWarning').text('Veuillez choisir une quantité de billet positive');
            $('#ticketsQtyMessageWarning').show(0);
        }
        else if ($qty>0 && $date==""){
            $('#ticketsQtyMessageWarning').text('');
            $('#ticketsQtyMessageWarning').hide(0);
        }
        else if ($qty>0 && $date !=""){
            $('#ticketsQtyMessageWarning').text('');
            $('#ticketsQtyMessageWarning').hide(0);
            $('#openDaysInformationMessage').hide();
            $('#PricesInformationMessage').show(0);
            resetTickets();
            validateBtn();
            displayTickets();
            halfDayTicket();
            checkQtyOfDay();

        }
        else if ($qty<=0 && $date !=""){
            $('#ticketsQtyMessageInfo').hide(0);
            $('#ticketsQtyMessageWarning').text('Veuillez choisir une quantité de billet positive');
            $('#ticketsQtyMessageWarning').show(0);
            resetTickets();
            validateBtn();
        }
    })

});

//Add a ticket to the page
function addTicket(index){

    var $container =$('#order_tickets');
    var $template = $container.attr('data-prototype')
        .replace(/__name__label__/g, 'Billet n°' + (index)).replace(/class="control-label required">Billet/g,'class="control-label">Billet').replace(/__name__/g, index);

    var $prototype = $($template);
    addDeleteLink($prototype);
    $container.append($prototype);
}

//Remove all tickets from the page
function resetTickets(){
    $index = 0;
    var $container = document.getElementById("order_tickets");
    var $tickets = $container.childNodes;
    var ticketsJQ=$($tickets);
    ticketsJQ.remove();

}

//Add a delete button on ticket
function addDeleteLink($prototype) {

    var $deleteLink = $('<button type="button" class="btn btn-danger btn-xs glyphicon glyphicon-trash" title="Supprimer ce billet" data-toggle="modal" style="margin-right:5px"></button>');
    $prototype.prepend($deleteLink);
    $deleteLink.click(function (e) {
        var $qty =$('#order_quantity').val();
        $('#order_quantity').val($qty-1);
        $prototype.remove();
        e.preventDefault();
        return false;
    });
}

//Check if enough tickets left
function checkQtyOfDay(){
    var $date = $('.bookingDatepicker').val();
    var $qty = $('#order_quantity').val();
    var DATA = 'date=' + $date;
    $.ajax({
        type:"POST",
        url:"tickets-vendus",
        data:DATA,
        cache:false,
        dataType:'json',
        success:function(response){
            if((response['ticketsLeft']- $qty)<0) {
                $('#ticketsQtyMessageInfo').hide(0);
                $('#ticketsQtyMessageWarning').html('<p><strong>Info stock : </strong> quantité souhaitée de billets est supérieure à celle disponible pour la date choisie.<br /> Pour cette journée, il ne reste plus que ' + response['ticketsLeft'] +' billets.</p>');
                $('#ticketsQtyMessageWarning').show(0);
                $('#validate-btn').attr('disabled','disabled');
            }
            else{
                $('#ticketsQtyMessageWarning').hide(0);
                $('#ticketsQtyMessageInfo').show(0);
                $('#validate-btn').removeAttr('disabled');

            }
        }
    })
}

//display or remove loading spinner
var $loading = $('#loadingSpinner');
$(document)
    .ajaxStart(function () {
        $loading.show();
    })
    .ajaxStop(function () {
        $loading.hide();
    });

//Disabled paiement button if no quantity choosen
function validateBtn(){

    var $qty = $('#order_quantity').val();
    if ($qty >0) {
        $('#validate-btn').show();
    }
    else {
        $('#validate-btn').hide();
    }
}

//Disabled order quantity button if no date choosen
function qtyBtn(){

    var $date = $( ".bookingDatepicker").val();
    if($date=="") {
        $('#order_quantity').attr({
            'disabled': 'disabled',
            'title': 'Renseignez en premier lieu la date souhaitée de votre visite'
        });
    }
}

//Display tickets depending of the selected qty
function displayTickets(){
    var $qty =$('#order_quantity').val();
    var $index = 0;
    for (i=0;i<$qty;i++) {
        $index = i + 1;
        addTicket($index);
    }
}

//Check the booking date and the hour to display half-day ticket or not
function halfDayTicket(){
    var $today = new Date();
    var $selectedDate = $('.bookingDatepicker').val();
    //Convert to JS date
    $selectedYear = $selectedDate.charAt(6)+$selectedDate.charAt(7)+$selectedDate.charAt(8)+$selectedDate.charAt(9);
    $selectedMonth = $selectedDate.charAt(3)+$selectedDate.charAt(4);
    $selectedDay = $selectedDate.charAt(0)+$selectedDate.charAt(1);
    $selectedDate = $selectedYear + '/' + $selectedMonth +'/' + $selectedDay;
    $selectedDate = new Date($selectedDate);

    //If same day and above 2 pm
    if($selectedDate.getFullYear()==$today.getFullYear() && $selectedDate.getMonth()==$today.getMonth() && $selectedDate.getDate()==$today.getDate() && $today.getHours()>=14 ){
        $('.halfOrFull_1').remove();
        $('.fullOrHalfDay').removeClass('required');
        $('.fullOrHalfDay').attr('title','Demi-tarif automatique à partir de 14h00');
        $('.halfOrFull_0').attr('title','Demi-tarif automatique à partir de 14h00');
    }
    else{
        //Add the required class if it was removed
        if (!$('.fullOrHalfDay').hasClass('required')) {
            $('.fullOrHalfDay').addClass('required');
            $('.fullOrHalfDay').removeAttr('title');
            $('.halfOrFull_0').removeAttr('title');
        }
        //Add the full day option
        if(document.getElementsByClassName('halfOrFull_1').length == 0) {
            var $fullDay = '<option value ="1" class ="halfOrFull_1">Billet journée</option>'
            $('.halfOrFull_0').before($fullDay);
        }
    }

}


