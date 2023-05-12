<!DOCTYPE html>
<html>
    <head>
        <!-- This makes the title and adds in the CSS stylesheet -->
        <title><?php
        echo urldecode($_GET["site"]);
        ?></title> 
        <link rel="stylesheet", href="style.css"></link>
        <!-- This adds Leaflet, a JavaScript library for maps -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"
        integrity="sha256-kLaT2GOSpHechhsozzB+flnD+zUyjE2LlfWPgU04xyI="
        crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"
        integrity="sha256-WBkoXOwTeyKclOHuWtc+i2uENFpDZ9YPdf5Hf+D7ewM="
        crossorigin=""></script>
    </head>
    <body>
        <div class="header"><p id="headertext">Ukrainian Site Status</p></div>
        <?php
        // Sets the API URL based off the site
        $url = "http://ip-api.com/json/" . urldecode($_GET["site"]);
        // This get the HTTP headers for the site, which provides information about the connection
        $headers = get_headers("http://" . $_GET["site"]);
        // This gets the status code from the HTTP headers
        $status_code = substr($headers[0], 9, 3);
        // This will get the JSON file that tells us location information from the the API
        $json = json_decode(file_get_contents($url));
        // This will tell the user if the site is online or offline based on the HTTP status code
        if ($status_code == '200' || $status_code == '301' || $status_code == '307' || $status_code == '302') {
            echo "<p class='title'>Website <a class='sublight'>" . urldecode($_GET["site"]) . "</a> is online!</p>";
        } else {
            echo "<p class='title'>Website <a class='sublight'>" . urldecode($_GET["site"]) . "</a> is offline!</p>";
            echo $status_code;
        }
        ?>
        <div class="darkensub">
            <p class="title">Site information for <a class="sublight">
                <?php
                // Just gets the site name
                echo urldecode($_GET["site"]);
                ?>
            </a></p>
            <?php
            // Writes the location, ISP and IP address to the webpage
            echo "<p class='standard'><b>Location: </b>" . $json->city . ", " . $json->regionName . ", " . $json->country . "</p>";
            echo "<p class='standard'><b>Internet Service Provider: </b>" . $json->isp . "</p>"; 
            echo "<p class='standard'><b>Site IP: </b>" . $json->query . "</p>"; 
            // Hidden info for JavaScript to process.
            echo '<p id="lat">' . $json->lat . "</p>";
            echo '<p id="lon">' . $json->lon . "</p>";
            ?>
            <div id="locationmap"></div>
            <script>
                // This sets up the Leaflet map, then connects it to OpenStreetMap's tile server
                var map = L.map('locationmap').setView([parseFloat(document.getElementById("lat").innerHTML), parseFloat(document.getElementById("lon").innerHTML)], 10)
                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 20,
                    attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
                }).addTo(map); 
                // This adds a popup to the approximate location of the server. This is primarily for when the approximate location is a small town.
                L.popup().setLatLng([parseFloat(document.getElementById("lat").innerHTML), parseFloat(document.getElementById("lon").innerHTML)]).setContent("This site's servers are approximately around here.").openOn(map);
                // This adds a circle around the area.
                L.circle([parseFloat(document.getElementById("lat").innerHTML), parseFloat(document.getElementById("lon").innerHTML)], {
                    color: 'red',
                    fillColor: '#b37717',
                    fillOpacity: 0.5,
                    radius: 7500
                }).addTo(map)
            </script>
        </div>
    </body>
</html>