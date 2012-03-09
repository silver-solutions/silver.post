<?php

/**
 * MVC Router for silver.post extension
 *
 * @copyright Copyright (c) 2012 silver.solutions GmbH. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */
class svrPostApiProvider implements ezpRestProviderInterface {

    /**
     * Returns registered versioned routes for provider
     *
     * @return array Associative array. Key is the route name (beware of name collision !). Value is the versioned route.
     */
    public function getRoutes() {
        $routes = array(
            'svrStatus'          => new ezpRestVersionedRoute( new ezcMvcRailsRoute( '/status', 'svrPostController', 'status' ), 1 ),
            'svrAddChildNode'    => new ezpRestVersionedRoute( new ezcMvcRailsRoute( '/content/addchild/:nodeId', 'svrPostController', 'addChildNode' ), 1 ),
            'svrGetAllowedNodes' => new ezpRestVersionedRoute( new ezcMvcRailsRoute( '/content/getallowednodes', 'svrPostController', 'getAllowedNodes' ), 1 ),
        );
        return $routes;
    }

    /**
     * Returns associated with provider view controller
     *
     * @return ezpRestViewController
     */
    public function getViewController() {
        return new svrPostApiViewController();
    }
}
