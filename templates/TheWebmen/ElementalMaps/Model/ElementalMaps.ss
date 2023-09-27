<div class="google-map" <% if $MapHeight %>style="height:$MapHeight"<% end_if %> data-latitude="$Latitude" data-longitude="$Longitude" data-zoom="$MapZoom" data-type="$MapType" data-mapid="$MapID" data-iconurl="$MarkerIcon.URL" data-iconwidth="$MarkerIconWidth" data-iconheight="$MarkerIconHeight">
    <% if $Markers %>
        <script class="map-markers" type="text/json">
            [
            <% loop $Markers %>
                {
                    "latitude": $Latitude,
                    "longitude": $Longitude,
                    "title": "$Title",
                    "link": <% if $Link %>"$Link.LinkURL"<% else %>false<% end_if %>
                }<% if not $Last %>,<% end_if %>
            <% end_loop %>
            ]
        </script>
    <% end_if %>
</div>
<script>
    function initGoogleMap() {
        var maps = document.getElementsByClassName('google-map');
        var numMaps = maps.length;
        for(var i = 0; i < numMaps; i++) {
            var mapElement = maps[i];
            var markersData = mapElement.querySelector('.map-markers');
            var map = new google.maps.Map(mapElement, {
                center: {
                    lat: parseFloat(mapElement.dataset.latitude),
                    lng: parseFloat(mapElement.dataset.longitude)
                },
                zoom: parseFloat(mapElement.dataset.zoom),
                mapTypeId: mapElement.dataset.type,
                mapId: mapElement.dataset.mapid
            });

            if(markersData){
                markersData = JSON.parse(markersData.textContent);
                var numMarkers = markersData.length;
                for(var j = 0; j < numMarkers; j++){
                    var marker = new google.maps.Marker({
                        position: {
                            lat: parseFloat(markersData[j].latitude),
                            lng: parseFloat(markersData[j].longitude)
                        },
                        map: map,
                        title: markersData[j].title,
                        allData: markersData[j]
                    });
                    if(mapElement.dataset.iconurl){
                        marker.set('icon', mapElement.dataset.iconurl);
                    };
                    if(mapElement.dataset.iconwidth && mapElement.dataset.iconheight){
                        marker.set('icon', {
                            url: mapElement.dataset.iconurl,
                            scaledSize: new google.maps.Size(mapElement.dataset.iconwidth,mapElement.dataset.iconheight)
                        });
                    };
                    marker.addListener('click', function(e) {
                        if(this.allData.link){
                            window.location = this.allData.link;
                        }
                    });
                }
            }
        }
    }

    function waitForGoogle() {
        if (window.google) {
            initGoogleMap();
        } else {
            setTimeout(waitForGoogle.bind(this), 550);
        }
    }

    waitForGoogle();
</script>