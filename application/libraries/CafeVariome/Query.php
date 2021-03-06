<?php

require_once "CafeVariome.php";

class Query extends CafeVariome {

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
        $query_data = $query['query'];

        $query_array = array();
        foreach ($query_data as $k => $v) {
            foreach ($v as $element) {
                if (!$this->syntax == "elasticsearch")
                    continue;

                if($k == "coordinate") {
                    $type = $element['reference_type'];

                    if($type == "genome") {
                        $chr = substr(explode(".",$element['reference']['id'])[0], 3);
                        $build = explode(".",$element['reference']['id'])[1];    
                    }
                    $element['operator'] = strtolower($element['operator']);
                    $start = $element['start'];
                    $stop = $element['stop'];
                    if($element['operator'] == "exact") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:" . $start . " AND genome_stop_d:" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:" . $start . " AND accession_stop_d:" . $stop;
                        }
                    } else if($element['operator'] == "exceed") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:<" . $start . " AND genome_stop_d:>" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:<" . $start . " AND accession_stop_d:>" . $stop;
                        }
                    } else if($element['operator'] == "begin_between") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:>=" . $start . " AND genome_start_d:<=" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:>=" . $start . " AND accession_start_d:<=" . $stop;
                        }
                    } else if($element['operator'] == "end_between") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_stop_d:>=" . $start . " AND genome_stop_d:<=" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_stop_d:>=" . $start . " AND accession_stop_d:<=" . $stop;
                        }
                    } else if($element['operator'] == "begin_and_end_between") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:>=" . $start . " AND genome_stop_d:<=" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:>=" . $start . " AND accession_stop_d:<=" . $stop;
                        }
                    } else if($element['operator'] == "only_begin_between") { 
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:>=" . $start . " AND genome_start_d:<=" . $stop . " AND genome_stop_d:>" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:>=" . $start . " AND accession_start_d:<=" . $stop . " AND accession_stop_d:>" . $stop;
                        }
                    } else if($element['operator'] == "only_end_between") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:<" . $start . " AND genome_stop_d:>=" . $start . " AND genome_stop_d:<=" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:<" . $start . " AND accession_stop_d:>=" . $start . " AND accession_stop_d:<=" . $stop;
                        }
                    } else if($element['operator'] == "begin_at_start") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_start_d:" . $start;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_start_d:" . $start;
                        }
                    } else if($element['operator'] == "end_at_stop") {
                        if($type == "genome") {
                            $query_array[$element['querySegmentID']] = "(genome_chr:" . $chr . " OR genome_chr:chr" . $chr . ") AND genome_build:" . $build . " AND genome_stop_d:" . $stop;
                        } else {
                            $query_array[$element['querySegmentID']] = "accession_ref:" . $element['reference']['id'] . " AND accession_stop_d:" . $stop;
                        }
                    }
                } else if ($k == "otherFields") {
                    $element['operator'] = strtolower($element['operator']);
                    $element['otherField'] = strtolower($element['otherField']);
                    $element['otherValue'] = strtolower($element['otherValue']);

                    if ($element['operator'] == "is")
                        $query_array[$element['querySegmentID']] = $element['otherField'] . ":" . $element['otherValue'];
                    else if ($element['operator'] == "is like")
                        $query_array[$element['querySegmentID']] = $element['otherField'] . ":*" . $element['otherValue'] . "*";
                    else if ($element['operator'] == "is not")
                        $query_array[$element['querySegmentID']] = $element['otherField'] . ":(-" . $element['otherValue'] . ")";
                    else if ($element['operator'] == "is not like")
                        $query_array[$element['querySegmentID']] = $element['otherField'] . ":(-*" . $element['otherValue'] . "*)";
                    else {
                        $element['otherValue'] = str_replace('-', '\-', $element['otherValue']); // Escape
                        $element['otherValue'] = str_replace('+', '\+', $element['otherValue']); // Escape
                        if ($element['operator'] == "=" && is_numeric($element['otherValue'])) {
                            $query_array[$element['querySegmentID']] = $element['otherField'] . "_d:" . $element['otherValue'];
                        } else if ($element['operator'] == "!=" && is_numeric($element['otherValue'])) {
                            $query_array[$element['querySegmentID']] = $element['otherField'] . "_d:(" . "<" . $element['otherValue'] . " OR >" . $element['otherValue'] . ")";
                        } else {
                            $query_array[$element['querySegmentID']] = $element['otherField'] . "_d:" . "" . $element['operator'] . "" . $element['otherValue'];
                        }
                    }
                } else if ($k == "phenotypeFeature") {
//                    error_log("phenotype feature || otherFields : " . $k);
                    $attribute = $element['phenotypeConcept']['cursivePhenotypeConcept']['term'];
                    $value = strtolower($element['phenotypeFeature']['value']);

                    $attribute = str_replace(' ', '_', $attribute); // Replace spaces with underscore as this is how the phenotype attribute is indexed in ElasticSearch (ElasticSearch can't handle spaces in a field name so have removed spaces and replaced with underscore)
                    $attribute = str_replace('[', '\[', $attribute); // Escape square brackets as these are reserved in ElasticSearch
                    $attribute = str_replace(']', '\]', $attribute); // Escape square brackets as these are reserved in ElasticSearch

                    if (strtolower($element['operator']) == "is") {
//                        error_log("is");
                        if (strtolower($value) == "null") {
                            $query_array[$element['querySegmentID']] = "_missing_:" . $attribute;
                        } else {
                            $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                            $query_array[$element['querySegmentID']] = $attribute . "_raw:" . $value;
                        }
                    } else if (strtolower($element['operator']) == "is like") {
//                        error_log("is like");
                        $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                        $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "*" . $value . "*";
                    } else if (strtolower($element['operator']) == "is not") {
//                        error_log("is not");
                        if (strtolower($value) == "null") {
                            $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                        } else {
                            $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                            $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "(-" . $value . ")";
                        }
                    } else if (strtolower($element['operator']) == "is not like") {
//                        error_log("is nt like");
                        if (strtolower($value) == "null") {
                            $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                        } else {
                            $value = addcslashes($value, '-+=&&||><!\(\)\{\}\[\]^"~*?:\\');
                            $query_array[$element['querySegmentID']] = $attribute . "_raw:" . "(-*" . $value . "*)";
                        }
                    } else if (strtolower($element['operator']) == "=") {
//                        error_log("=");
                        if (strtolower($value) == "null") {
                            $query_array[$element['querySegmentID']] = "_missing_:" . $attribute;
                        } else {
                            if (is_numeric($value)) {
                                $value = str_replace('-', '\-', $value); // Escape
                                $value = str_replace('+', '\+', $value); // Escape
                                $query_array[$element['querySegmentID']] = $attribute . "_d:" . $value;
                            }
                        }
                    } else if (strtolower($element['operator']) == "!=") {
//                        error_log("!=");
                        if (strtolower($value) == "null") {
                            $query_array[$element['querySegmentID']] = "_exists_:" . $attribute;
                        } else {
                            if (is_numeric($value)) {
                                $value = str_replace('-', '\-', $value); // Escape
                                $value = str_replace('+', '\+', $value); // Escape
                                $query_array[$element['querySegmentID']] = $attribute . "_d:(" . "<" . $value . " OR >" . $value . ")";
                            }
                        }
                    } else { // Else it must be a numeric comparison >,<,>=,<=
//                        error_log(">,<,>=,<=");
                        if (is_numeric($value)) {
                            $value = str_replace('-', '\-', $value); // Escape
                            $value = str_replace('+', '\+', $value); // Escape
                            $query_array[$element['querySegmentID']] = $attribute . "_d:" . "" . $element['operator'] . "" . $value;
                        } else { // A string value with numeric comparison shouldn't be possible as it's blocked in the query builder
                            $query_array[$element['querySegmentID']] = $attribute . ":" . " " . $element['operator'] . "" . $value;
                        }
                    }
                } else if (strtolower($element['operator']) == "is") {
                    if ($k == "sequence")
                        $query_array[$element['querySegmentID']] = ($element['molecule'] == "DNA" ? "dna_sequence:" : "protein_sequence:") . $element['sequence'];
                    else if ($k == "geneSymbol")
                        $query_array[$element['querySegmentID']] = "gene_symbol:" . $element['geneSymbol']['symbol'];
                    else if ($k == "hgvsName")
                        $query_array[$element['querySegmentID']] = "(hgvs_reference:" . $element['reference']['id'] . " AND hgvs_name:" . $element['hgvsName'] . ")";
                } else if (strtolower($element['operator']) == "is like") {
                    if ($k == "geneSymbol")
                        $query_array[$element['querySegmentID']] = "gene_symbol:*" . $element['geneSymbol']['symbol'] . "*";
                }
            }
        }

//        error_log("query array -> " . print_r($query_array, 1));

        $query_statement = $query['queryStatement'];
//        error_log("QUERY STATEMENT -> $query_statement");
//        Add hashes to make sure that numbers on their own don't get replace (e.g. BRCA2 would get replaced if there's a statement ID of 2 after first initial)
        $query_statement = preg_replace('/\b(\d+)\b/', "##$1##", $query_statement);
//		error_log("queryStatement: $query_statement");
        foreach ($query_array as $statement_id => $query_element) {
            $statement_id = "##" . $statement_id . "##";
            $query_element = "##(" . $query_element . ")##";
//            error_log("BEFORE query_element -> $statement_id -> $query_element -> $query_statement");
            $query_statement = preg_replace("/$statement_id/", "$query_element", $query_statement);
//            error_log("AFTER query_element -> $statement_id -> $query_element -> $query_statement");
        }
        $query_statement = str_replace('##', '', $query_statement);
        error_log("query_statement -> $query_statement");

        $query_statement_for_display = $query_statement;
        $query_statement_for_display = str_replace('_d', '', $query_statement_for_display); // Remove the appended numeric index name so that it isn't displayed to the user
        $query_statement_for_display = str_replace('_raw', '', $query_statement_for_display);
        $query_statement_for_display = str_replace('_missing_', 'missing', $query_statement_for_display);
        $query_statement_for_display = str_replace('_exists_', 'exists', $query_statement_for_display);
        $query_statement_for_display = str_replace('\[', '[', $query_statement_for_display);
        $query_statement_for_display = str_replace('\]', ']', $query_statement_for_display);
//        $query_statement_for_display = str_replace('_', ' ', $query_statement_for_display);
        // print "<h4>$query_statement_for_display</h4>";
        print "<h4>" . $query['queryString'] . "</h4>";
        return $query_statement;
    }

}
