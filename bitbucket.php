<?php
// Make sure we have a payload, stop if we do not.
if( ! isset( $_POST['payload'] ) )
	die( '<h1>No payload present</h1><p>A BitBucket POST payload is required to deploy from this script.</p>' );

/**
 * Tell the script this is an active end point.
 */
define( 'ACTIVE_DEPLOY_ENDPOINT', true );

require_once 'deploy-config.php';
/**
 * Deploys BitBucket git repos
 */
class BitBucket_Deploy extends Deploy {
	/**
	 * Decodes and validates the data from bitbucket and calls the 
	 * deploy constructor to deploy the new code.
	 *
	 * @param 	string 	$payload 	The JSON encoded payload data.
	 */
        function __construct( $payload ) {
                $payload = json_decode( $_POST['payload'], true );
                $name = $payload['repository']['name'];
                $branch = $payload['commits'][0]['branch'];
                if ( isset( parent::$repos[ $name ] ) ) {
			$data = parent::$repos[ $name ];
                        $data['commit'] = $payload['commits'][0]['node'];
                        $data['message'] = $payload['commits'][0]['message'];
                        if (is_array($data['branch'])) {
                                foreach ($data['branch'] as $br => $pa) {
                                	if (strpos($branch, $br) === 0 ) {
                                        	$data['path'] = $pa;
                                        	$data['branch'] = $branch;
                                        	parent::__construct( $name, $data );
                                        	return;
                                    	}
                                }
                        } else if (strpos($branch, $data['branch']) === 0) {
                        	$data['branch'] = $branch;
                                parent::__construct($name, $data);
                                return;
                        }
			$this->log( "Branch " . $branch . " not found in config." );
                }
		$this->log( "Repository " . $name . " not found in config." );
        }
}
// Start the deploy attempt.
new BitBucket_Deploy( $_POST['payload'] );
