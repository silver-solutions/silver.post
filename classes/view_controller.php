<?php

/**
 * MVC View Controller for silver.post extension
 *
 * @author Markus Birth <mab@silversolutions.de>
 * @copyright Copyright (c) 2012 silver.solutions GmbH. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */
class svrPostApiViewController implements ezpRestViewControllerInterface {
    /**
     * Creates a view required by controller's result
     *
     * @param ezcMvcRoutingInformation $routeInfo
     * @param ezcMvcRequest $request
     * @param ezcMvcResult $result
     * @return ezcMvcView
     * @since 2012-03-09
     */
    public function loadView( ezcMvcRoutingInformation $routeInfo, ezcMvcRequest $request, ezcMvcResult $result ) {
        return new ezpRestJsonView( $request, $result );
    }

}
