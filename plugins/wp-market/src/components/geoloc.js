/*
 TODO : 
  - Modification de la recherche : change filters location, distance
  - recadrage de la map lors d'une nouvelle recherche
*/


// Global settings
const MapBoxAPIkey    = '';
const GoogleMapAPIkey = '';
const distanceDefault = 10;
let map = '';

// https://developer.mozilla.org/en-US/docs/Web/API/Geolocation_API/Using_the_Geolocation_API
function geoLocation() {

  const status = document.querySelector('#status');
  const filters = document.querySelector('.search-filters-text');
  const filtersLink = document.querySelector('.search-filters-edit-link');
  const markets = document.querySelector('ul.markets');
  const formzip = document.querySelector('.form-location')

  let api_url;

  filters.innerHTML = '';
  markets.innerHTML = '';


  if(!navigator.geolocation) {
    status.textContent = 'Geolocalisation non supportée sur votre navigateur.';
    formzip.classList.remove('hidden');    
  } 
  else {
    // Get user location by browser
    status.textContent = 'Localisation en cours ...';    
    navigator.geolocation.getCurrentPosition(success, error);    
  }

  // location by browser ok
  function success(position) {   
    getMarkets(position.coords.latitude, position.coords.longitude); 
  }

  // location by browser not ok
  function error() {
    //status.textContent = 'Echec de la localisation.';
    status.textContent = 'Veuillez saisir votre code postal.';
    formzip.classList.remove('hidden');
  }


}

function getLocationByZipcode(e) {
  e.preventDefault();
  
  const zipcode = document.querySelector('input#zipcode').value;
  const distance = document.querySelector('select#distance').value
 

  if(zipcode) {
    // get markets by zipcode
    api_url = location.protocol + '//nominatim.openstreetmap.org/search?format=json&q='+zipcode+',France';
    console.log(api_url);
    fetch( api_url , {
      method: 'GET',
      cache: 'default',
    })
    .then((res)  => res.json())
    .then((data) => {        
      getMarkets( data[0].lat, data[0].lon, distance);     
    })
    
  } 
}

// Get Markets
function getMarkets( latitute, longitude, distance ) {
  
  const status = document.querySelector('#status');
  const filters = document.querySelector('.search-filters-text');
  const filtersLink = document.querySelector('.search-filters-edit-link');
  const markets = document.querySelector('ul.markets');

  if(!distance) { distance = distanceDefault; }
  
  // Api url
  let api_url = window.location.origin + '/lesconnectes/wp-json/api/markets';
  api_url +=  '?lat='+ latitute +'&lng='+longitude+'&dist='+distance;

  let params = new URLSearchParams(api_url);
  distance = params.get("dist");     

  // update contents
  status.textContent = 'Recherche des marchés à proximité';
  markets.innerHTML = '';

  filters.innerHTML = 'Votre position :  '+ latitute +', '+longitude;
  filters.innerHTML += '<br>Distance : '+distance+' Km.'; 


  // fetch API
  fetch( api_url , {
    method: 'GET',
    cache: 'default',
  })
  .then((res)  => res.json())
  .then((data) => {
    
    if(!data) {
      markets.innerHTML = 'Aucun résultat pour cette recherche.';
    }
    else {

      status.textContent = '';
      filtersLink.classList.remove('hidden');

      // update contents
      data.forEach( item => {
        markets.innerHTML += '<li><a href="">'+item.post_title+'</a><br>Distance : '+Number(item.distance).toFixed(2)+' Km</li>';
      })

      document.querySelector('.markets-count').textContent = data.length+' résultats';


      // Load / refresh map
        if(GoogleMapAPIkey) {
          initMap( latitute, longitude, data);
        } 
        else if (MapBoxAPIkey){
          initOpenStreetMap( latitute, longitude, data, distance);
        } else {
          console.log('API not found. Needed to load matching map');
        }
    }



  })
  
}

// openstreetmaps : https://leafletjs.com/examples/quick-start/
function initOpenStreetMap(lat, lng, items, distance) {
  
    // init
    if(!map) {      
      map = L.map('map').setView([lat, lng], 15);
    } else {
      // clear current map
      map.eachLayer((layer) => {
        layer.remove();
      });
    }

    // view position
    map.flyTo([lat, lng], 10, {
        animate: true,
        duration: 2 // seconds
    });

  
  // markers

    // items markers
    items.forEach( item => {
      var marker = L.marker([item.locLat, item.locLong]).addTo(map);
      var markerlink = '<br><a href="">Afficher plus d\'informations</a>';
      marker.bindPopup("<b>"+item.post_title+"</b><br>Distance : "+Number(item.distance).toFixed(2)+" Km" + markerlink).openPopup();
    });

    // user marker
    var marker = L.marker([lat, lng]).addTo(map);
    marker.bindPopup("<b>Vous êtes ici</b>").openPopup();

  // Layer
  L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token={accessToken}', {
      attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
      maxZoom: 18,
      id: 'mapbox/streets-v11',
      tileSize: 512,
      zoomOffset:-1,
      accessToken: MapBoxAPIkey
  }).addTo(map);

}

// Google maps
function initMap(lat, lng, items) {
  // init map
  const myLatLng = { lat: lat, lng: lng };
  const map = new google.maps.Map(document.getElementById("googlemap"), {
    zoom: 4,
    center: myLatLng,
  });

  // markers
  items.forEach( item => {
    new google.maps.Marker({
      position: myLatLng,
      map,
      title: item.post_title,
      color: 'blue'
    });

  })

  new google.maps.Marker({
    position: myLatLng,
    map,
    title: "Vous êtes ici.",
  });

}

// button : find near me
document.querySelector('#find-me').addEventListener('click', geoLocation);

// button : form zipcode submit
document.querySelector('#form-zipcode').addEventListener('submit', getLocationByZipcode, false);

