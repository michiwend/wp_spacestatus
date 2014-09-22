<?php
/*  Copyright (c) 2014  Michael Wendland
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *  Authors:
 *      Michael Wendland <michael@michiwend.com>
 */


// callAPI returns either the response or an error.
function callAPI($url) {
    // FIXME set timeout, useragent, etc.
    $api_response = wp_remote_get( $url );

    $rsp_code = wp_remote_retrieve_response_code( $api_response );
    $rsp_msg  = wp_remote_retrieve_response_message( $api_response );
    $rsp_body = wp_remote_retrieve_body( $api_response );

    // If request wasn't successfull return error.
    if( $rsp_code != 200) {
        return new WP_ERROR(
            'api_call_failed',
            "Failed calling ".$url,
            $rsp_msg );
    }

    return New APIResponse( $rsp_body );
}


// APIResponse holds the decoded data and provides api-version independend
// methods to get the fields needed in this plugin.
class APIResponse {

    private $rsp;

    function __construct($data) {
        $this->rsp = json_decode( $data );
    }

    function getSpaceStatus() {
        if( $this->getAPIVersion() < 13 ) {
            return $this->rsp->open;
        }
        else {
            return $this->rsp->state->open;
        }
    }

    function getAPIVersion() {
        return $this->rsp->api * 100;
    }

    function getIconOpen() {
        if( $this->getAPIVersion() < 13 ) {
            return $this->rsp->icon->open;
        }
        else {
            return $this->rsp->state->icon->open;
        }
    }

    function getIconClosed() {
        if( $this->getAPIVersion() < 13 ) {
            return $this->rsp->icon->closed;
        }
        else {
            return $this->rsp->state->icon->closed;
        }
    }
}

?>
