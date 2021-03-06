<?php

/**
 *
 * @author owen
 */
require_once "CafeVariome.php";

class Query_old extends CafeVariome {

    function __construct($parameters) {
//		parent::__construct();
        $this->CI = & get_instance();
        if (array_key_exists('syntax', $parameters)) {
            $this->syntax = $parameters['syntax'];
        } else {
            $this->syntax = 'elasticsearch';
        }
    }

    function parse($query) {
//		error_log("query -> " . print_r($query, 1));
//		$query_metadata = $query['queryMetadata'];
//		$query_id = $query_metadata['queryId'];
//		$query_type = $query_metadata['queryType'];
//		$query_label = $query_metadata['label'];
//		$submitter_id = $query_metadata['submitter']['id'];
//		$submitter_name = $query_metadata['submitter']['name'];
//		$submitter_email = $query_metadata['submitter']['email'];
//		$submitter_institution = $query_metadata['submitter']['institution'];
        $query_data = $query['query'];

//		error_log("query_data -> " . json_encode($query, 1));
        $query_array = array();
        foreach ($query_data as $k => $v) {
//			error_log("1 -> $k -> " . print_r($v,1));
            foreach ($v as $element) {
//				error_log("2 -> " . print_r($element,1));
                if ($this->syntax == "elasticsearch") {

                    if (strtolower($element['operator']) == "is") {
                        if ($k == 'phenotypeFeature') {
//							error_log("phenofeature -> " . print_r($element, 1));
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);
//							error_log("----> $attribute -> $value");
                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            if (strtolower($value) == "null") {
                                $query_array[$element['querySegmentID']] = "_missing_:" . $attribute;
                            } else {
                                $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                                $query_array[$element['querySegmentID']] = $attribute . "_raw:" . $value;
                            }
                        } elseif ($k == "geneSymbol") {
//							error_log("geneSymbol ---> " . print_r($element['geneSymbol'],1));
                            $query_array[$element['querySegmentID']] = "gene_symbol:" . $element['geneSymbol']['symbol']; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							error_log("geneSymbol -> " . $element['geneSymbol']['symbol']);
                        } elseif ($k == "hgvsName") {
                            $query_array[$element['querySegmentID']] = "hgvs:" . $element['hgvsName'];
//							$query_array[$element['querySegmentID']] = "hgvs:" . $element['hgvsName']; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							error_log("hgvsName ---> " . $element['hgvsName']);
                        } else {
                            $query_array[$element['querySegmentID']] = $element[$k]; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
                        }
                    } elseif (strtolower($element['operator']) == "is like") {
                        if ($k == 'phenotypeFeature') {
//							error_log("phenofeature -> " . print_r($element, 1));
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);
//							error_log("----> $attribute -> $value");
                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                            $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "*" . $value . "*";
                        } elseif ($k == "geneSymbol") {
//							error_log("geneSymbol ---> " . print_r($element['geneSymbol'],1));
                            $query_array[$element['querySegmentID']] = "gene_symbol:" . "*" . $element['geneSymbol']['symbol'] . "*";
//							error_log("geneSymbol -> " . $element['geneSymbol']['symbol']);
                        }
                    } elseif (strtolower($element['operator']) == "is not") {
                        if ($k == 'phenotypeFeature') {
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);
                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch


                            if (strtolower($value) == "null") {
                                $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                            } else {
                                $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                                $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "(-" . $value . ")";
                            }
                        }
                    } elseif (strtolower($element['operator']) == "is not like") {
                        if ($k == 'phenotypeFeature') {
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);

                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch


                            if (strtolower($value) == "null") {
                                $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                            } else {
                                $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                                $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "(-*" . $value . "*)";
                            }
                        }
                    } elseif (strtolower($element['operator']) == "=") {
                        if ($k == 'phenotypeFeature') {
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);

                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
//							$subject = '+ - = && || > < ! ( ) { } [ ] ^ " ~ * ? : \ /';
//							$result = preg_replace('%([+\-&|!(){}[\]^"~*?:/]+)%', '\\\\$1', $subject);
                            if (strtolower($value) == "null") {
                                $query_array[$element['querySegmentID']] = "_missing_:" . $attribute;
                            } else {
                                if (is_numeric($value)) {
                                    $value = str_replace('-', '\-', $value); // Escape
                                    $value = str_replace('+', '\+', $value); // Escape
                                    $query_array[$element['querySegmentID']] = $attribute . "_d:" . $value;
                                }
                            }
                        }
                    } elseif (strtolower($element['operator']) == "!=") {
//					elseif ( htmlentities($element->operator) == "&ne;" ) {
                        if ($k == 'phenotypeFeature') {
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);

                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch

                            if (strtolower($value) == "null") {
                                $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                            } else {
                                if (is_numeric($value)) {
                                    $value = str_replace('-', '\-', $value); // Escape
                                    $value = str_replace('+', '\+', $value); // Escape
                                    $query_array[$element['querySegmentID']] = $attribute . "_d:(" . "<" . $value . " OR >" . $value . ")";
                                }
                            }
                        }
                    } else { // Else it must be a numeric comparison >,<,>=,<=
                        if ($k == 'phenotypeFeature') {
                            $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                            $value = strtolower($element['phenotypeFeature']['value']);

                            $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                            $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                            if (is_numeric($value)) {
                                $value = str_replace('-', '\-', $value); // Escape
                                $value = str_replace('+', '\+', $value); // Escape
                                $query_array[$element['querySegmentID']] = $attribute . "_d:" . "" . $element['operator'] . "" . $value;
                            } else { // A string value with numeric comparison shouldn't be possible as it's blocked in the query builder
                                $query_array[$element['querySegmentID']] = $attribute . ":" . " " . $element['operator'] . "" . $value;
                            }
                        }
                    }
                }
            }
        }
        error_log("query array -> " . print_r($query_array, 1));

        $query_statement = $query['queryStatement'];
//		error_log("QUERY STATEMENT -> $query_statement");
        // Add hashes to make sure that numbers on their own don't get replace (e.g. BRCA2 would get replaced if there's a statement ID of 2 after first initial)
        $query_statement = preg_replace('/\b(\d+)\b/', "##$1##", $query_statement);
//		str_replace(array("(",")"),array("-",""),$number);
//		error_log("queryStatement: $query_statement");
        foreach ($query_array as $statement_id => $query_element) {
            $statement_id = "##" . $statement_id . "##";
            $query_element = "##" . $query_element . "##";
            error_log("BEFORE query_element -> $statement_id -> $query_element -> $query_statement");
            $query_statement = preg_replace("/$statement_id/", "$query_element", $query_statement);
            error_log("AFTER query_element -> $statement_id -> $query_element -> $query_statement");
        }
        $query_statement = str_replace('##', '', $query_statement);
//		error_log("query_statement -> $query_statement");

        $query_statement_for_display = $query_statement;
        $query_statement_for_display = str_replace('_d', '', $query_statement_for_display); // Remove the appended numeric index name so that it isn't displayed to the user
        $query_statement_for_display = str_replace('_raw', '', $query_statement_for_display);
        $query_statement_for_display = str_replace('_missing_', 'missing', $query_statement_for_display);
        $query_statement_for_display = str_replace('_exists_', 'exists', $query_statement_for_display);
        $query_statement_for_display = str_replace('\[', '[', $query_statement_for_display);
        $query_statement_for_display = str_replace('\]', ']', $query_statement_for_display);
        $query_statement_for_display = str_replace('_', ' ', $query_statement_for_display);
        print "<h4>$query_statement_for_display</h4>";
        return $query_statement;
    }

    function parse_operator() {
        if (strtolower($element['operator']) == "is") {
            if ($k == 'phenotypeFeature') {
                error_log("phenofeature -> " . print_r($element, 1));
                $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                $value = $element['phenotypeFeature']['value'];
                error_log("----> $attribute -> $value");
                $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                if (strtolower($value) == "null") {
                    $query_array[$element['querySegmentID']] = "_missing_:" . $attribute;
                } else {
                    $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                    $query_array[$element['querySegmentID']] = $attribute . "_raw:" . $value;
                }
            } elseif ($k == "geneSymbol") {
//							error_log("GS ---> " . print_r($element['geneSymbol'],1));
                $query_array[$element['querySegmentID']] = "gene_symbol:" . $element['geneSymbol']['symbol']; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							error_log("gene symbol -> " . $element['geneSymbol']['symbol']);
            } else {
                $query_array[$element['querySegmentID']] = $element[$k]; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							$query_array[$element->id] = $element->{$k}; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } elseif (strtolower($element['operator']) == "is like") {
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
//							$element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
//							$element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
                $element->{$k} = addcslashes($element->{$k}, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                $query_array[$element->querySegmentID] = $attribute . "_raw:" . "*" . $element->{$k} . "*";
            } else {
//							$element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
//							$element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
                $element->{$k} = addcslashes($element->{$k}, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                $query_array[$element->querySegmentID] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							$query_array[$element->id] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } elseif (strtolower($element->operator) == "is not") {
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch


                if (strtolower($element->{$k}) == "null") {
                    $query_array[$element->parameterID] = "_exists_:" . $attribute;
                } else {
                    $element->{$k} = addcslashes($element->{$k}, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                    $query_array[$element->parameterID] = $attribute . "_raw:" . "(-" . $element->{$k} . ")";
                }
            } else {
                $query_array[$element->parameterID] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							$query_array[$element->id] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } elseif (strtolower($element->operator) == "is not like") {
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch


                if (strtolower($element->{$k}) == "null") {
                    $query_array[$element->parameterID] = "_exists_:" . $attribute;
                } else {
//								$element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
//								$element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
//								$element->{$k} = preg_replace('/-|/','-',$element->{$k});
//								$element->{$k} = preg_replace('%([+\-&|!(){}[\]^"~*?:/]+)%', '\\\\$1', $element->{$k});
//								$element->{$k} = preg_replace('%([+-=]+)%', '\\\\$1', $element->{$k});
//								$elasticsearch_escaped_characters = array (+ - = && || > < ! ( ) { } [ ] ^ " ~ * ? : \ /);
                    $element->{$k} = addcslashes($element->{$k}, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                    $query_array[$element->parameterID] = $attribute . "_raw:" . "(-*" . $element->{$k} . "*)";
                }
            } else {
                $query_array[$element->parameterID] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//							$query_array[$element->id] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } elseif (strtolower($element->operator) == "=") {
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                if (strtolower($element->{$k}) == "null") {
                    $query_array[$element->parameterID] = "_missing_:" . $attribute;
                } else {
                    if (is_numeric($element->{$k})) {
                        $element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
                        $element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
                        $query_array[$element->parameterID] = $attribute . "_d:" . $element->{$k};
                    } else { // A string value with numeric comparison shouldn't be possible as it's blocked in the query builder
                        $query_array[$element->parameterID] = $attribute . ":" . $element->{$k};
                    }
                }
            } else {
//				$element->{$k} = addcslashes($element->{$k},'-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                $query_array[$element->parameterID] = $element->{$k}; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
//				$query_array[$element->id] = $element->{$k}; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } elseif (strtolower($element->operator) == "!=") {
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch

                if (strtolower($element->{$k}) == "null") {
                    $query_array[$element->parameterID] = "_exists_:" . $attribute;
                } else {
                    if (is_numeric($element->{$k})) {
                        $element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
                        $element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
                        $query_array[$element->parameterID] = $attribute . "_d:(" . "<" . $element->{$k} . " OR >" . $element->{$k} . ")";
                    }
                }
            } else {
                $query_array[$element->parameterID] = "*" . $element->{$k} . "*"; // Get query term using the value of the object name as the key (it's dynamic so need the curly brackets) then set this as the value in the query array and the key is the parameterID
            }
        } else { // Else it must be a numeric comparison >,<,>=,<=
            if ($k == 'phenotype_epad') {
                $attribute = str_replace(' ', '_', $element->attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                if (is_numeric($element->{$k})) {
                    $element->{$k} = str_replace('-', '\-', $element->{$k}); // Escape
                    $element->{$k} = str_replace('+', '\+', $element->{$k}); // Escape
                    $query_array[$element->parameterID] = $attribute . "_d:" . "" . $element->operator . "" . $element->{$k};
                } else { // A string value with numeric comparison shouldn't be possible as it's blocked in the query builder
                    $query_array[$element->parameterID] = $attribute . ":" . " " . $element->operator . "" . $element->{$k};
                }
            } else {
                $query_array[$element->parameterID] = $element->{$k};
            }
        }
    }

    function run($term, $source) {
//		error_log("term -> $term");
//		$term = "(Long_nose:present) AND (Narrow_nasal_ridge:present)";
//		$term = "nose_length_\[cm\]:>5";
//		$term = "(nose_length_\[m\]:>=5) AND (nose_length_\[mm\]:>=6)";
//		error_log("term -> $term");
        if ($this->syntax == "elasticsearch") {
            // Get dynamic name for the ES index to try and avoid clashes with multiple instance of CV on the same server
            $es_index = $this->CI->config->item('site_title');
            $es_index = preg_replace('/\s+/', '', $es_index);
            $es_index = strtolower($es_index);
            $this->CI->elasticsearch->set_index($es_index);
            $this->CI->elasticsearch->set_type("variants");
            $query = array();
            $query['size'] = 0;
            $term = urldecode($term);

            $search_fields = $this->CI->settings_model->getSearchFields("search_fields");

            if (!empty($search_fields)) { // Specific search fields are specified in admin interface so only search on these
                $search_fields_elasticsearch = array();
                foreach ($search_fields as $fields) {
                    $search_fields_elasticsearch[] = $fields['field_name'];
                }
//				error_log("search fields -> " . print_r($search_fields, 1));
                $query['query']['bool']['must'][] = array('query_string' => array("fields" => $search_fields_elasticsearch, "query" => "$term", 'default_operator' => "AND"));
            } else { // Otherwise search across all fields
//				if ( property_exists($this, 'notFilter') ) {
//					error_log("notFilter -> " . $this->notFilter);
//					$query['query']['bool']['must'][] = array('query_string' => array("query" => "$term", 'default_operator' => "AND")); // , "default_field" => "" Hack: default_field as empty because when doing apoe not M it was searching the gender field and getting back the hits for that
//					$query['query']['bool']['must_not'][] = array('query_string' => array("query" => "$this->notFilter", 'default_operator' => "AND")); // , "default_field" => "" Hack: default_field as empty because when doing apoe not M it was searching the gender field and getting back the hits for that
//				}
//				else {
                $query['query']['bool']['must'][] = array('query_string' => array("query" => "$term", 'default_operator' => "AND")); // 'analyzer' => 'not_analyzed' , "default_field" => "" Hack: default_field as empty because when doing apoe not M it was searching the gender field and getting back the hits for that
//				}
//				$query['query']['query_string'] = array("query" => "$term", 'default_operator' => "AND");
//				$query['query']['bool']['must_not'][] = array('query_string' => array("query" => "$term"));
//				$query['query']['bool']['must_not'][] = array('query_string' => array("query" => "$term", 'default_operator' => "AND"));
            }

            $query['query']['bool']['must'][] = array("term" => array("source" => $source));
            $query['facets']['sharing_policy']['terms'] = array('field' => "sharing_policy");
//			$query['filter']['not'] = array();
//			$query['query']['bool']['must'][] = array("term" => array("source" => $source));
            $query = json_encode($query);
//			error_log("query ----> $query $source");
            $es_data = $this->CI->elasticsearch->query_dsl($query);
//			error_log(print_r($es_data, 1));
            $counts = array();
//			print "SOURCE -> $source<br />";
            if (array_key_exists('facets', $es_data)) {
                foreach ($es_data['facets']['sharing_policy']['terms'] as $facet_sharing_policy) {
                    $sp_es = $facet_sharing_policy['term'];
                    if ($sp_es == "openaccess") {
                        $sp_es = "openAccess";
                    } else if ($sp_es == "restrictedaccess") {
                        $sp_es = "restrictedAccess";
                    } else if ($sp_es == "linkedaccess") {
                        $sp_es = "linkedAccess";
                    }

                    $counts[$sp_es] = $facet_sharing_policy['count'];
//					error_log("es counts -> " . print_r($counts,1));
//					print "<br />";
                }
            }
            return $counts;
        }
    }

    function run_API($source_uri, $source, $term) {
        $this->load->model('federated_model');
        // Get the node name and then remove it from the source name - need to do this since the node name has been appended in order to make it unique for this node - in the node that is to be search it won't have this appended bit
        $node_name = $this->federated_model->getNodeNameFromNodeURI($source_uri);
        $node_source = str_replace("_" . $node_name, "", $source);
        $federated_data = array(
            'term' => $term,
            'source' => $node_source
        );
//		error_log("federated_data -> " . $term . " -> " . $source_uri . " -> " . print_r($federated_data, 1));
//		$counts = federatedAPI($source_uri, $federated_data);
        $term = urlencode($term);
//		error_log("term -> " . $term);
        $counts = @file_get_contents($source_uri . "/discover/variantcount/$term/$node_source/json");
//		error_log($source_uri . "/discover/variantcount/$term/$source/json");
//		error_log("decode -> " . json_decode($counts));
        $counts = json_decode($counts, TRUE);
        $hacked_counts = array();
        if (!empty($counts)) {
            foreach ($counts as $key => $value) {
                foreach ($value as $k => $v) {
//					error_log("key: $k value: $v");
                    $hacked_counts[$k] = $v;
                }
            }
        }
//		error_log("counts from federatedAPI -> " . print_r($counts, 1));
//		error_log("hacked -> " . print_r($hacked_counts, 1));
        return $hacked_counts;
    }

    function detect_type($element, $data) {
        switch ($element) {
            case "allele":
                echo "Running allele query -> ";
                $this->allele_query($data);
                break;
            case "geneSymbol":
                echo "Running gene symbol query -> ";
                $this->gene_symbol_query($data);
                break;
            case "green":
                echo "Your favorite color is green!";
                break;
            default:
                echo "Query type was not detected";
        }
    }

    function allele_query($data) {
        foreach ($data as $allele) {

//			$operator = $allele->operator;
            $operator = isset($allele->operator) ? $allele->operator : '';

//			$source = $allele->source;
            $source = isset($allele->source) ? $allele->source : '';

//			$reference = $allele->reference;
            $reference = isset($allele->reference) ? $allele->reference : '';

//			$start = $allele->start;
            $start = isset($allele->start) ? $allele->start : '';

//			$end = $allele->end;
            $end = isset($allele->end) ? $allele->end : '';

//			$allele_sequence = $allele->allele_sequence;
            $allele_sequence = isset($allele->allele_sequence) ? $allele->allele_sequence : '';

            if (is_array($allele_sequence)) {
                echo "ARRAY";
            } else {
                echo "NOT ARRAY";
            }
        }
        print_r($data);
    }

    function gene_symbol_query($data) {
        $gene_symbol = isset($allele->geneSymbol) ? $allele->geneSymbol : '';
        print_r($data);
    }

}

?>
