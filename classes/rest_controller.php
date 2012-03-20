<?php

/**
 * MVC Controller for silver.post extension
 *
 * @author Markus Birth <mab@silversolutions.de>
 * @copyright Copyright (c) 2012 silver.solutions GmbH. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0
 */
class svrPostController extends ezcMvcController {

    /**
     * Sets a variable "status" to the value "OK" (string). Used to check if the REST interface is working.
     * @return \ezcMvcResult Variable "status" = "OK"
     * @since 2012-03-09
     */
    public function doStatus() {
        $result = new ezcMvcResult();
        $result->variables['status'] = 'OK';
        return $result;
    }

    /**
     * Adds a new article item to the current node's children. Parameters: headline, teaser, text, image
     * @return \ezcMvcResult "status" = "OK"|"ERROR", if "OK": Variables "contentobject_id", "main_node_id", "url_alias" and "url" to the new node
     * @since 2012-03-09
     */
    public function doAddChildNode() {
        // needed for Publishing, index.php:336
        $moduleRepositories = eZModule::activeModuleRepositories();
        eZModule::setGlobalPathList( $moduleRepositories );

        $result = new ezcMvcResult();
        $user = eZUser::currentUser();

        self::log( 'POST: ' . print_r( $this->request->post, true ) );

        $params = array(
            'creator_id'       => $user->ContentObjectID,
            'class_identifier' => 'article',
            'parent_node_id'   => $this->nodeId,
            'attributes' => array(
                'title' => $this->request->post['headline'],
                'intro' => self::wrapXML( $this->request->post['teaser'] ),   // XML
                'body'  => self::wrapXML( $this->request->post['text'] ),   // XML
            ),
            'language' => 'eng-GB',
        );

        if ( isset( $this->request->files[0] ) ) {
            $params['attributes']['image'] = $this->request->files[0]->tmpPath;
        }

        self::log( 'eZ Params: ' . print_r( $params, true ) );

        $cobj = eZContentFunctions::createAndPublishObject( $params );
        if ( $cobj ) {
            $result->variables['status'] = 'OK';

            $node = $cobj->MainNode();
            $result->variables['contentobject_id'] = $cobj->attribute( 'id' );
            $result->variables['main_node_id']     = $node->attribute( 'id' );
            $result->variables['url_alias']        = $node->attribute( 'url_alias' );

            $url = $node->attribute( 'url_alias' );
            eZURI::transformURI( $url, false, 'full' );

            $result->variables['url'] = $url;
            self::log( 'POST OK: ' . $url );
        } else {
            self::log( 'ERROR: ' . print_r( $cobj, true ) );
            $result->variables['status'] = 'ERROR';
        }

        return $result;
    }

    /**
     * Returns a list of all allowed nodes to post in (read from silver.post.ini)
     * @return \ezcMvcResult Allowed nodes in "allowed_nodes", count in "allowed_nodes_count".
     * @since 2012-03-09
     */
    public function doGetAllowedNodes() {
        $sr_ini = eZINI::instance( 'silver.post.ini' );
        $allowed_nodes = $sr_ini->variable( 'NodeSettings', 'PostAllowed' );

        $result = new ezcMvcResult();
        $result->variables['allowed_nodes_count'] = count( $allowed_nodes );
        $result->variables['allowed_nodes']       = $allowed_nodes;
        $result->variables['status']              = 'OK';
        return $result;
    }

    /**
     * Creates a eZXML wrapping around the given String
     * @param string $txt Text content to convert to eZXML
     * @return string eZXML-type string
     * @since 2012-03-09
     */
    public static function wrapXML( $txt ) {
        return '<?xml version="1.0" encoding="utf-8"?><section xmlns:image="http://ez.no/namespaces/ezpublish3/image/" xmlns:xhtml="http://ez.no/namespaces/ezpublish3/xhtml/" xmlns:custom="http://ez.no/namespaces/ezpublish3/custom/"><paragraph><line xmlns:tmp="http://ez.no/namespaces/ezpublish3/temporary/">' . $txt . '</line></paragraph></section>';
    }

    /**
     * Logs entry to var/log/rest.log
     * @param string $txt Log message
     * @since 2012-03-09
     */
    public static function log( $txt ) {
        $stamp = date( 'Y-m-d H:i.s' );
        file_put_contents( 'var/log/rest.log', '[' . $stamp .'] ' . $txt, FILE_APPEND );
    }

}
