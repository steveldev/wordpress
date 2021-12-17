# Require Authentication for All Requests
https://developer.wordpress.org/rest-api/frequently-asked-questions/#require-authentication-for-all-requests

You can require authentication for all REST API requests by adding an is_user_logged_in check to the rest_authentication_errors filter.

**Note:** The incoming callback parameter can be either null, a WP_Error, or a boolean. The type of the parameter indicates the state of authentication:

**null:** no authentication check has yet been performed, and the hook callback may apply custom authentication logic.
**boolean:** indicates a previous authentication method check was performed. Boolean true indicates the request was successfully authenticated, and boolean false indicates authentication failed.
**WP_Error:** Some kind of error was encountered.

	
```
add_filter( 'rest_authentication_errors', function( $result ) {

    // If a previous authentication check was applied,
    // pass that result along without modification.
    if ( true === $result || is_wp_error( $result ) ) {
        return $result;
    }
 
    // No authentication has been performed yet.
    // Return an error if user is not logged in.
    if ( ! is_user_logged_in() ) {
        return new WP_Error(
            'rest_not_logged_in',
            __( 'You are not currently logged in.' ),
            array( 'status' => 401 )
        );
    }
 
    // Our custom authentication check should have no effect
    // on logged-in requests
    return $result;
});
```
