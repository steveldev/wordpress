# WP MARKET
  
## Installation
- Install WP

## Configuration
- Configure WP
- Activate multisite (subdomain)
- Add plugins : Woocommerce

## Custom plugins

Note :  Plugin prefix match core type (wp = wordpress, wc = woocommerce)  

### wp-market : 
- Create custom post type market and metaboxes
- Add menu : Markets

### wp-market-api :  
- API for post type market 
- API returns data from location

### wp-import-data : 
- Import data in WP from external API FLASK (with mongoDB)

### wp-search : 
- Search Market from location
- Geolocation
- OpenStreetMap / GoogleMap
- Add shortcode [search_market]

### wc-shipping-zones-market : 
- Add Menus "Points de vente" + "Market"
- Add Submenu "Market" in Woocommerce > Settings > Shipping
- Add form market : user can link a market with the store

### wc-user-services-manager
- Add user account menu items (services, assistance, parrainage)
- User account page "services" : list services and matching button action
- User account page "assistance" : TO DO
- User account page "parrainage" : TO DO
- Service Configuration page : TO DO
