<?php

/**
 * To add search to a controller, create a search() method with the following
 * code:
 *    function search() {
 * $this->redirect(array('action'=>'index','query' =>
 * $this->params['url']['query']));
 * }
 *
 * This will pass the query to the index, which then contains the conditions for
 * the DB query.
 * Example:
 *
 *    function index() {
 * $this->City->recursive = 0;
 *
 * if (isset($this->params['named']['query'])) {
 * $query = $this->Sanitize->escape($this->params['named']['query']);
 * $conditions = array(
 * 'OR' => array(
 * 'cityName LIKE' => '%'.$query.'%',
 * ),
 * );
 *
 * $this->set('query',$query);
 * } else {
 * $conditions = array();
 * }
 *
 * $this->paginate = array(
 * 'conditions' => $conditions,
 * );
 *
 * $this->set('cities', $this->paginate());
 * }
 *
 * For this controller, create a section in the if statement in the index method
 * below matching the controller name.
 * Re-create the conditions above, but instead of using the normal model name,
 * use AjaxSearch as the model name. This is a bit messy,
 * but I'll improve it in the future.
 *
 * The goal of this was to speed up the ajax search by creating a simple model
 * that didn't require a bunch of other models to operate.
 * Speed for search on the clients controller, for example, went from 1.8s down
 * to 250ms.
 *
 * TODO: Unify search conditions for both controller and ajax_search_controller
 * without compromising too much speed.
 *
 */
class AjaxSearchController extends AppController
{
    function index()
    {
        $this->layout = "ajax";

        if (isset($this->params['url']['query'])) {
            $this->params['form']['query'] = $this->params['url']['query'];
        }

        if (!empty($this->params['form']['query'])) {
            $query = $this->Sanitize->escape($this->params['form']['query']);
            $searchtype = $this->Sanitize->escape($this->params['url']['searchtype']);
            $queryPieces = explode(" ", $query);

            $sqlquery = '';
            foreach ($queryPieces as $piece) {
                if (strlen($piece) > 2) {
                    $sqlquery .= '+';
                }
                $sqlquery .= $piece . '* ';
            }

            if ($searchtype == "clients" || $searchtype == 'generator' || $searchtype == 'selectclients') {
                $this->AjaxSearch->table = 'clientNames';
                $this->AjaxSearch->primaryKey = 'clientId';
                $this->AjaxSearch->cacheClientNames();

                $params = array(
                    'fields' => array(
                        'name',
                        'clientId'
                    ),
                    'conditions' => array('OR' => array(
                        'clientId LIKE' => '%' . $query . '%',
                        'name' => $query,
                        'MATCH(name) AGAINST("' . $sqlquery . '" IN BOOLEAN MODE)'
                    )),
                    'limit' => 10,
                );
            } elseif ($searchtype == "users") {
                $this->AjaxSearch->table = 'user';
                $this->AjaxSearch->primaryKey = 'userId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("userSiteExtended", "userId");

                $conditions = array();
                $order = array();
                $fields = array(
                    'SimpleModel.username',
                    'AjaxSearch.userId',
                    'AjaxSearch.firstName',
                    'AjaxSearch.lastName',
                    'AjaxSearch.email',
                    'AjaxSearch.inactive'
                );

                if (intval($query) !== 0) {
                    $conditions['OR']['AjaxSearch.userId LIKE'] = '%' . $query . '%';
                } elseif (strpos($query, " ") === FALSE) {
                    $noSpace = true;

                    if (strpos($query, "@")) {
                        $isEmail = true;
                        $fields[] = "(CASE WHEN AjaxSearch.email LIKE '" . $query . "' THEN 1 ELSE 0 END) AS emailmatch";
                        $conditions['OR']['AjaxSearch.email LIKE'] = '%' . $query . '%';
                        $order[] = 'emailmatch DESC';
                    } else {
                        $order[] = 'usernamematch DESC';
                        $fields[] = "(CASE WHEN SimpleModel.username LIKE '" . $query . "' THEN 1 ELSE 0 END) AS usernamematch";
                        $conditions['OR']['SimpleModel.username LIKE'] = "%$query%";
                    }
                } elseif (strpos($query, " ")) {
                    $conditions['OR']['CONCAT(AjaxSearch.firstName, " ", AjaxSearch.lastName) LIKE'] = '%' . $query . '%';
                }

                $params = array(
                    'fields' => $fields,
                    'limit' => 10,
                    'order' => $order,
                    'conditions' => $conditions
                );
            } elseif ($searchtype == "tickets") {
                $this->AjaxSearch->table = 'ticket';
                $this->AjaxSearch->primaryKey = 'userId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("userSiteExtended", "userId");

                $params = array(
                    'fields' => array(
                        'AjaxSearch.ticketId',
                        'SimpleModel.username',
                    ),
                    'conditions' => array('OR' => array(
                        'SimpleModel.username LIKE' => '' . $query . '%',
                        'AjaxSearch.userId LIKE' => '' . $query . '%',
                        'ticketId LIKE' => '' . $query . '%',
                        'ticketId' => $query
                    ),),
                    'limit' => 10,
                );
            } elseif ($searchtype == "credit_trackings") {
                $this->AjaxSearch->table = 'creditTracking';
                $this->AjaxSearch->primaryKey = 'userId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("userSiteExtended", "userId");

                $params = array(
                    'fields' => array(
                        'AjaxSearch.userId',
                        'AjaxSearch.balance',
                        'SimpleModel.username',
                    ),
                    'conditions' => array('OR' => array(
                        'AjaxSearch.userId LIKE' => '%' . $query . '%',
                        'AjaxSearch.userId' => $query,
                        'SimpleModel.username LIKE' => '%' . $query . '%',
                    ),),
                    'limit' => 10,
                    'group' => 'AjaxSearch.userId',
                );
            } elseif ($searchtype == "cities") {
                $this->AjaxSearch->table = 'cityNew';
                $this->AjaxSearch->primaryKey = 'countryId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("countryNew", "countryId");

                $params = array(
                    'fields' => array(
                        'cityId',
                        'cityName',
                        'stateId',
                        'SimpleModel.countryName',
                        "MATCH (cityName) AGAINST('" . $query . "') as relevance",
                    ),
                    'conditions' => array(
                        'OR' => array('cityName LIKE' => '%' . $query . '%',),
                        'AND' => array('AjaxSearch.countryId = SimpleModel.countryId',)
                    ),
                    'limit' => 10,
                    'order' => 'relevance DESC'
                );
            } elseif ($searchtype == "states") {
                $this->AjaxSearch->table = 'stateNew';
                $this->AjaxSearch->primaryKey = 'countryId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("countryNew", "countryId");

                $params = array(
                    'fields' => array(
                        'stateId',
                        'stateName',
                        'SimpleModel.countryName',
                        "MATCH (stateName) AGAINST('" . $query . "') as relevance",
                    ),
                    'conditions' => array(
                        'OR' => array(
                            'stateName LIKE' => '%' . $query . '%',
                            'stateCode LIKE' => '%' . $query . '%',
                        ),
                        'AND' => array('AjaxSearch.countryId = SimpleModel.countryId',)
                    ),
                    'limit' => 10,
                    'order' => 'relevance DESC'
                );
            } elseif ($searchtype == "countries") {
                $this->AjaxSearch->table = 'countryNew';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $params = array(
                    'fields' => array(
                        'countryId',
                        'countryName',
                        "MATCH (countryName) AGAINST('" . $query . "') as relevance",
                    ),
                    'conditions' => array('OR' => array(
                        'countryName LIKE' => '%' . $query . '%',
                        'countryId LIKE' => '%' . $query . '%',
                    ),),
                    'limit' => 10,
                    'order' => 'relevance DESC'
                );
            } elseif ($searchtype == "search_redirects") {
                $this->AjaxSearch->table = 'searchRedirect';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $params = array(
                    'fields' => array(
                        'keyword',
                        'searchRedirectId',
                    ),
                    'conditions' => array('OR' => array('keyword LIKE' => '%' . $query . '%',),)
                );
            } elseif ($searchtype == "landing_pages") {
                $this->AjaxSearch->table = 'landingPage';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $params = array(
                    'fields' => array(
                        'landingPageId',
                        'landingPageName',
                    ),
                    'conditions' => array('OR' => array('landingPageName LIKE' => '%' . $query . '%',),)
                );
            } elseif ($searchtype == "bids") {
                $this->AjaxSearch->table = 'bid';
                $this->AjaxSearch->primaryKey = 'userId';

                // Readonly db config
                $this->AjaxSearch->useReadonlyDb();

                $this->loadSimple("userSiteExtended", "userId");

                $params = array(
                    'fields' => array(
                        'bidId',
                        'AjaxSearch.userId',
                        'SimpleModel.username',
                    ),
                    'conditions' => array('OR' => array(
                        'bidId LIKE' => '%' . $query . '%',
                        'SimpleModel.username LIKE' => '%' . $query . '%',
                        'AjaxSearch.userId LIKE' => '%' . $query . '%',
                    ),)
                );
            } else {
                $results = "Invalid search type";
            }

            if (!empty($params)) {
                $results = $this->AjaxSearch->find('all', $params);
                $this->set('query', $query);
                $this->set('results', $results);
                $this->render($searchtype);
            }
        }
    }

    function loadSimple($tableName, $primaryKey)
    {
        $this->loadModel("SimpleModel", array(
            $tableName,
            $primaryKey,
            "AjaxSearch",
            $this->AjaxSearch->useDbConfig
        ));

        $this->AjaxSearch->bindModel(array('hasOne' => array('SimpleModel' => array('foreignKey' => $primaryKey))));
    }

    function clients_jsonp()
    {
        $json = array();
        if (isset($this->params['url']['name'])) {
            $query = $this->params['url']['name'];
            Configure::write('debug', 0);
            $this->layout = 'ajax';
            $results = $this->AjaxSearch->query('SELECT cl.*, ct.cityName FROM client cl LEFT JOIN city ct ON (cl.cityId = ct.cityId) WHERE (cl.name LIKE ?);', array($query . '%'));
            foreach ($results as $client) {
                if (isset($client['cl']['name']) && !empty($client['cl']['name'])) {
                    $cn = utf8_encode($client['cl']['name']);
                    $json[$cn] = array();
                    foreach (array('cl', 'ct') as $cx) {
                        foreach ($client[$cx] as $ck => $cv) {
                            if (!empty($ck) && !empty($cv)) {
                                $json[$cn][$ck] = is_string($cv) ? utf8_encode($cv) : $cv;
                            }
                        }
                    }
                }
            }
            $this->set('jsonp', $json);
        }
    }

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->LdapAuth->allow('clients_jsonp');
    }
}
