// Added lines to use wp.element instead of importing React
const { Component, render } = wp.element;

import Locations from './components/geoloc';

// Render the app inside our shortcode's #app div
render(
    <div>      
        <Locations />
    </div>,
    document.getElementById('app')
);