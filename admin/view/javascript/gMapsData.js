var newLoc;//создаем новый елем при клике
var zoneID;//тек зона
var nLocate;//координаты клика

var triangleCoords = [];
var paramMap = [];


paramMap[0] = {
    paths: triangleCoords,
    strokeColor: '#FF0000',
    strokeOpacity: 0.7,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0.35
}
paramMap[1] = {
    paths: triangleCoords,
    strokeColor: '#1f20ff',
    strokeOpacity: 0.7,
    strokeWeight: 2,
    fillColor: '#1f20ff',
    fillOpacity: 0.35
}
paramMap[2] = {
    paths: triangleCoords,
    strokeColor: '#20ff1b',
    strokeOpacity: 0.7,
    strokeWeight: 2,
    fillColor: '#20ff1b',
    fillOpacity: 0.35
}

// Construct the polygon.
var markerTriangle = [];
    markerTriangle[0] = new google.maps.Polygon(paramMap[0]);
    markerTriangle[1] = new google.maps.Polygon(paramMap[1]);
    markerTriangle[2] = new google.maps.Polygon(paramMap[2]);
var map;

$(document).ready(function() {
    if($('#tab-map').length){ initMap(); }
    $('#mapsDatA').on('click', function () {
        setTimeout(function () {
            google.maps.event.trigger(map, 'resize');
        },300);
    });
    $('.refreshMap').on('click', function () {
        $(document).resize();
        google.maps.event.trigger(map, 'resize');
    });

    $('.refreshMap.btn').on('click', function () {  refreshMap(); })
    $('.hid-panel').on('click', function () {
        var panel = $(this).parent().parent().parent();
        var sh = $(this).hasClass('shows');
        if(sh){
            $(panel).find('.panel-body').addClass('hidden');
            $(this).removeClass('shows');
        }else{
            $(panel).find('.panel-body').removeClass('hidden');
            $(this).addClass('shows');
        }
    })
    $('.btn-metka-clear').on('click', function () {
        var panel = $(this).parent().parent().next('.panel-body');
        var data = panel[0].children[0].children[0].children[1];
        $(data).empty();
        refreshMap();
    })
    $('.panel-heading .panel-title').on('click',function () {
        var panel = $(this).parent();
        var inp = $(this).parent().find('input.poli-add');
        var sel = inp[0].checked;

        if(sel){
            panel.removeClass('enabl');
        }else{
            $('.panel-heading.enabl').removeClass('enabl');
            $('input.poli-add:checked').prop('checked',false);
            panel.addClass('enabl');
            var panels = $(this).parent().next('.panel-body');
            zoneID = $(panels).attr('data-pol-id');

        }
        inp[0].checked = !sel;
    })

    $('.viewZone').on('click',function () {
        var panel = $(this).parent();
        var inp = $(this).parent().find('input.viewPoli');
        var sel = inp[0].checked;
        if(sel){
            $(this).removeClass('shows').html('<i class="fa fa-eye-slash"></i>');
        }else{
            $(this).addClass('shows').html('<i class="fa fa-eye"></i>');
        }
        inp[0].checked = !sel;
        refreshMap();
    })

    $('.btn.btn-metka-add').on('click',function () {
        var panel = $(this).parent().parent();
        var inp = panel.find('.poli-add');
        var sel = inp[0].checked;

        if(sel){
            panel.removeClass('enabl');
        }else{
            $('.panel-heading.enabl').removeClass('enabl');
            $('input.poli-add:checked').prop('checked',false);
            panel.addClass('enabl');
        }
        inp[0].checked = !sel;
        var panels = $(this).parent().parent().next('.panel-body');
        var nom = $(panels).attr('data-pol-id');
        var data = panels[0].children[0].children[0].children[1];
        addElPoli(data,nom);
    })

    $('.poligons-data').on('click', '.btn-metka-del', function () {
        var el = $(this).parent().parent().remove();
        refreshMap();
    })
    $('.setMapPoli').on('click', function () {
        var panel = $(this).parent();
        var inp = $(this).parent().find('input#newPoli');
        var sel = inp[0].checked;

        if(sel){
            $(this).removeClass('enabl');
        }else{
            $(this).addClass('enabl');
        }
        inp[0].checked = !sel;
        newLoc = inp[0].checked
        console.log(newLoc);
    })
});
function initMap() {
    var lat = parseFloat( $('#map_pos_lat').val());
    var lng = parseFloat( $('#map_pos_lng').val());
    var zoom = parseInt( $('#map_pos_zoom').val());
    if(zoom == 0)zoom = 8;
    if(lat == 0) lat = 50.44263847959072;
    if(lng == 0) lng = 30.5474853515625;
    var posit = {lat: lat,lng:lng};

    map = new google.maps.Map(document.getElementById('mapsMarkerForms'), {
        zoom: zoom,
        center: posit,
        mapTypeId: 'terrain'
    });

    map.addListener("click", function(e) {
        var loc = e.latLng;
        nLocate = {lat: parseFloat(loc.lat()),lng: parseFloat(loc.lng())};
       setPol();
    });

    map.addListener('zoom_changed', function() {
        if($('.map-position-data input.map_pos_enabl:checked')){
            $('#map_pos_zoom').val( parseInt( map.getZoom()) );
            console.log('Zoom: ' + map.getZoom());
        }
    });
    refreshMap();
}

function setPol() {
    if(zoneID == undefined) zoneID = 0;
    var panel = $('.poligon-panel');
    var tab = $(panel[zoneID]).find('.panel-body');
    var el = tab[0].children[0].children[0].children[1];
    if(newLoc){
        addNewElPoli(el,zoneID,nLocate);
    }else{

    }
    refreshMap();
    // var el = $('input.map_pos_enabl:checked').parent().parent();
    // $(el).find('.map_pos_lat').val(parseFloat(nLocate.lat()));
    // $(el).find('.map_pos_lng').val(parseFloat(nLocate.lng()));
}

function refreshMap() {
    var zone = $('.poligon-panel');
    $.each(zone, function ( index, value ) {
        markerTriangle[index].setMap(null);
        var vid = $(this).find('.viewPoli').prop('checked');
        if(vid){
            var item = $(this).find('.poligons-data-item');
            triangleCoords = [];
            $.each(item, function ( index, value ) {
                var lat = parseFloat( $(this).find('.map_pos_lat').val());
                var lng = parseFloat( $(this).find('.map_pos_lng').val());

                var posit = {lat: lat,lng:lng};
                triangleCoords.push(posit);
            });
            paramMap[index].paths = triangleCoords;
            markerTriangle[index] = new google.maps.Polygon(paramMap[index]);
            markerTriangle[index].setMap(map);
        }

    });

    // //markerTriangle.setMap(null);
    // var item = $('.poligons-data-item');
    // triangleCoords = [];
    // $.each(item, function ( index, value ) {
    //     var lat = parseFloat( $(this).find('.map_pos_lat').val());
    //     var lng = parseFloat( $(this).find('.map_pos_lng').val());
    //
    //     var posit = {lat: lat,lng:lng};
    //     triangleCoords.push(posit);
    // });
    // //markerTriangle.paths = triangleCoords;
    // markerTriangle = new google.maps.Polygon({
    //     paths: triangleCoords,
    //     strokeColor: '#FF0000',
    //     strokeOpacity: 0.7,
    //     strokeWeight: 2,
    //     fillColor: '#FF0000',
    //     fillOpacity: 0.35
    // });
    // markerTriangle.setMap(map);
}

function addElPoli(el,nom) {
    var htm = '<tr class="poligons-data-item">\n' +
'                   <td>\n' +
'                       <input class="map_pos_enabl" type="radio" name="enabl_data" style="box-shadow: 0px 0px 2px black;" >\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <input type="text" name="map[poligon]['+nom+'][lat][]" class="map_pos_lat" style="width: 120px;">\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <input type="text" name="map[poligon]['+nom+'][lng][]"  class="map_pos_lng" style="width: 120px;">\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <span class="btn btn-danger btn-metka-del" title="удалить данную точку"><i class="fa fa-trash-o"></i></span>\n' +
'                   </td>\n' +
'              </tr>';
    $(el).append(htm);
    //$('.poligons-data').append(htm);
}
function addNewElPoli(el,nom,loc) {
    var htm = '<tr class="poligons-data-item">\n' +
'                   <td>\n' +
'                       <input class="map_pos_enabl" type="radio" name="enabl_data" style="box-shadow: 0px 0px 2px black;" checked >\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <input type="text" name="map[poligon]['+nom+'][lat][]" class="map_pos_lat" style="width: 120px;" value="'+loc.lat+'">\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <input type="text" name="map[poligon]['+nom+'][lng][]"  class="map_pos_lng" style="width: 120px;" value="'+loc.lng+'">\n' +
'                   </td>\n' +
'                   <td>\n' +
'                       <span class="btn btn-danger btn-metka-del" title="удалить данную точку"><i class="fa fa-trash-o"></i></span>\n' +
'                   </td>\n' +
'              </tr>';
    $(el).append(htm);
    //$('.poligons-data').append(htm);
}