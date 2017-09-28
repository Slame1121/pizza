var triangleCoords = [
    {lat: 25.774, lng: -80.190},
    {lat: 18.466, lng: -66.118},
    {lat: 32.321, lng: -64.757},
    {lat: 25.774, lng: -80.190}
];
// Construct the polygon.
var markerTriangle = new google.maps.Polygon({
    paths: triangleCoords,
    strokeColor: '#FF0000',
    strokeOpacity: 0.7,
    strokeWeight: 2,
    fillColor: '#FF0000',
    fillOpacity: 0.35
});
var map;

$(document).ready(function() {
    function initMap() {
        var lat = parseFloat( $('#map_pos_lat').val());
        var lng = parseFloat( $('#map_pos_lng').val());
        var zoom = parseInt( $('#map_pos_zoom').val());
        var posit = {lat: lat,lng:lng};

        map = new google.maps.Map(document.getElementById('mapsMarker'), {
            zoom: zoom,
            center: posit,
            mapTypeId: 'terrain'
        });

        map.addListener("click", function(e) {
            var location = e.latLng;
            var el = $('input.map_pos_enabl:checked').parent().parent();
            $(el).find('.map_pos_lat').val(parseFloat(location.lat()));
            $(el).find('.map_pos_lng').val(parseFloat(location.lng()));
        });

        map.addListener('zoom_changed', function() {
            if($('.map-position-data input.map_pos_enabl:checked')){
                $('#map_pos_zoom').val( parseInt( map.getZoom()) );
                console.log('Zoom: ' + map.getZoom());
            }
        });
        markerTriangle.setMap(map);
    }

    $('.refreshMap.btn').on('click', function () {
        var item = $('.poligons-data-item');
        triangleCoords = [];
        $.each(item, function ( index, value ) {
            var lat = parseFloat( $(this).find('.map_pos_lat').val());
            var lng = parseFloat( $(this).find('.map_pos_lng').val());

            var posit = {lat: lat,lng:lng};
            triangleCoords.push(posit);
        });
        //markerTriangle.paths = triangleCoords;
        markerTriangle = new google.maps.Polygon({
            paths: triangleCoords,
            strokeColor: '#FF0000',
            strokeOpacity: 0.7,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35
        });
        markerTriangle.setMap(map);
    })

    if($('#tab-map').length){
        initMap();
    }
    $('.btn.btn-metka-add').on('click',function () {
        var htm = '<div class="col-sm-12 poligons-data-item" style="text-align: center;" >\n' +
            '        <div class="col-sm-4">\n' +
            '           <label class="col-sm-12 control-label">широта</label>\n' +
            '           <input type="text" name="map[poligon][lat][]" value="" class="map_pos_lat">\n' +
            '        </div>\n' +
            '        <div class="col-sm-4">\n' +
            '           <label class="col-sm-12 control-label">долгота</label>\n' +
            '           <input type="text" name="map[poligon][lng][]" value="" class="map_pos_lng">\n' +
            '        </div>\n' +
            '        <div class="col-sm-2">\n' +
            '           <label class="col-sm-12 control-label"  style="font-size: 10px;">Применить</label>\n' +
            '           <input class="map_pos_enabl" type="radio" name="enabl_data"  >\n' +
            '        </div>\n' +
            '        <span class="btn btn-metka-del" style="box-shadow: 0px 0px 2px black;">X</span>\n'+
            '        </div>';
        $('.poligons-data').append(htm);
    })
    $('.poligons-data').on('click', '.btn-metka-del', function () {
        var el = $(this).parent('.poligons-data-item').remove();
        console.log(this);
    })
});