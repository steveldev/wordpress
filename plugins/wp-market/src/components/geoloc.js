
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
      api_url +=  '?lat='+latitude+'&lng='+longitude+'&dist=20';

      let params = new URLSearchParams(api_url);
      let distance = params.get("dist"); 
      
      
      markets.textContent = `Distance : `+distance+' Km.';
      
      fetch( api_url, {
            method: 'GET',
            //mode: 'no-cors',
            cache: 'default',
            // credentials: 'omit', // include, *same-origin, omit
            //   body: JSON.stringify( {location: 76} )
      })
      .then((res)  => res.json())
      .then((data) => {
      
        let items = data;

        markets.innerHTML += '<ul>';
        items.forEach( item => {
          markets.innerHTML += '<li><a href="">'+item.post_title+' ('+Number(item.distance).toFixed(2)+' Km)</a></li>';
        })
        markets.innerHTML += '<ul>';
        console.log(items)
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
  
  }
  
  document.querySelector('#find-me').addEventListener('click', geoFindMe);
  
  