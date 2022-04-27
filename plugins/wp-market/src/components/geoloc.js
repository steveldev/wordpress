// Global settings
const MapBoxAPIkey    = '';
const GoogleMapAPIkey = '';
const distance = 50;


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

function getLocationByZipcode() {

  const zipcode = document.querySelector('input#zipcode').value;
  if(zipcode) {
    // get markets by zipcode
    api_url = location.protocol + '//nominatim.openstreetmap.org/search?format=json&q='+zipcode+',France';
    
    fetch( api_url , {
      method: 'GET',
      cache: 'default',
    })
    .then((res)  => res.json())
    .then((data) => {   
      const items = data; 
      getMarkets( items[0].lat, items[0].lon, distance);     
    }) 
    
  } 
}

// Get Markets
function getMarkets( latitute, longitude, distance ) {
  
  const status = document.querySelector('#status');
  const filters = document.querySelector('.search-filters-text');
  const filtersLink = document.querySelector('.search-filters-edit-link');
  const markets = document.querySelector('ul.markets');

  status.textContent = 'Recherche des marchés à proximité';

  filters.innerHTML = 'Votre position :  '+ latitute +', '+longitude;
  filters.innerHTML += '<br>Distance : '+distance+' Km.'; 
  
  // Api url
  let api_url = window.location.origin + '/lesconnectes/wp-json/api/markets';
  api_url +=  '?lat='+ latitute +'&lng='+longitude+'&dist='+distance;

  let params = new URLSearchParams(api_url);
  distance = params.get("dist");     


  // fetch API
  fetch( api_url , {
    method: 'GET',
    cache: 'default',
  })
  .then((res)  => res.json())
  .then((data) => {
    
    status.textContent = '';
    filtersLink.classList.remove('hidden');

    const items = data;
    console.log(items);


    items.forEach( item => {
      markets.innerHTML += '<li><a href="">'+item.post_title+'</a><br>Distance : '+Number(item.distance).toFixed(2)+' Km</li>';
    })

    document.querySelector('.markets-count').textContent = items.length+' résultats';


    // Load map
      if(GoogleMapAPIkey) {
        window.initMap = initMap(latitude, longitude, items);
      } 
      else if (MapBoxAPIkey){
        window.initOpenStreetMap = initOpenStreetMap( latitute, longitude, items, distance);
      } else {
        console.log('API not found. Needed to load matching map');
      }

  })
  
}

// openstreetmaps : https://leafletjs.com/examples/quick-start/
function initOpenStreetMap(lat, lng, items, distance) {
  
  // init map
  var map = L.map('map').setView([lat, lng], 9);
  
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

// button : find near me
document.querySelector('#find-me').addEventListener('click', geoLocation);

// button : form zipcode submit
document.querySelector('#form-zipcode').addEventListener('submit', getLocationByZipcode);

