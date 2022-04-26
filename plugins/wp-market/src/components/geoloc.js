
// https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API/Using_the_Geolocation_API

function geoFindMe() {

    const status = document.querySelector('#status');
    const mapLink = document.querySelector('#map-link');
    const markets = document.querySelector('#markets');
  
    mapLink.href = '';
    mapLink.textContent = '';
    markets.innerHTML = '';
  
    function success(position) {
      const latitude  = position.coords.latitude;
      const longitude = position.coords.longitude;
  
      status.textContent = '';
      mapLink.href = `https://www.openstreetmap.org/#map=18/${latitude}/${longitude}`;
      mapLink.textContent = `Votre position :  ${latitude} °, ${longitude} °`;

      markets.textContent = `Recherche des marchés à proximité`;
      
      // get market by location
      let api_url = window.location.origin + '/lesconnectes/wp-json/api/markets';
      api_url +=  '?lat='+latitude+'&lng='+longitude+'&dist=30';

      let params = new URLSearchParams(api_url);
      let distance = params.get("dist"); 
      
      
      markets.textContent = `Distance : `+distance+' Km.';
      

      // fetch API
      fetch( api_url, {
            method: 'GET',
            //mode: 'no-cors',
            cache: 'default',
            // credentials: 'omit', // include, *same-origin, omit
            //   body: JSON.stringify( {location: 76} )
      })
      .then((res)  => res.json())
      .then((data) => {
      
        const items = data;
        console.log(items);
        markets.innerHTML += '<ul>';
        items.forEach( item => {
          markets.innerHTML += '<li><a href="">'+item.post_title+' ('+Number(item.distance).toFixed(2)+' Km)</a></li>';
        })
        markets.innerHTML += '<ul>';

        // load Google Maps
      //  window.initMap = initMap(latitude, longitude, items);

        // load openstreetmaps
        window.initOpenStreetMap = initOpenStreetMap(latitude, longitude, items, distance);
      })

    



    }
  
    function error() {
      status.textContent = 'Echec de la localisation.';
    }
  
    if(!navigator.geolocation) {
      status.textContent = 'Geolocalisation non supportée sur votre appareil.';
    } else {
      status.textContent = 'Localisation en cours ...';
      navigator.geolocation.getCurrentPosition(success, error);

    }
  
    // openstreetmaps
    // https://leafletjs.com/examples/quick-start/
    function initOpenStreetMap(lat, lng, items, distance) {
      
      var map = L.map('map').setView([lat, lng], 9);
      
      // markers
        // user marker
        var marker = L.marker([lat, lng], {fillColor: 'red'}).addTo(map);

        // items markers
        items.forEach( item => {
          var marker = L.marker([item.locLat, item.locLong], {color: 'green'}).addTo(map);
        });




      L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
          attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
          maxZoom: 18,
          id: 'mapbox/streets-v11',
          tileSize: 512,
          zoomOffset:-1,
          accessToken: 'YOUR TOKEN HERE'
      }).addTo(map);

     


    }

    // Google maps
  function initMap(lat, lng, items) {
    
    const myLatLng = { lat: lat, lng: lng };
    const map = new google.maps.Map(document.getElementById("markets-map"), {
      zoom: 4,
      center: myLatLng,
    });
  
    items.forEach( item => {
      new google.maps.Marker({
        position: myLatLng,
        map,
        title: "Hello World!",
        color: 'blue'
      });

    })

      new google.maps.Marker({
        position: myLatLng,
        map,
        title: "Hello World!",
      });
  }

  }
  
  document.querySelector('#find-me').addEventListener('click', geoFindMe);
  


  
  //window.initMap = initMap;