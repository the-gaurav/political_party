<?php
    /**
    * Category : Update extra entities  
    * Using Solarium to get data form solr core 'constituency'
    * @date: 7 April 2016
    * @author:Gaurav
    * @Update:Sachin 14-05-2016
    * @Version : 1
    */

    include_once("./includes/admin_config.php");
    require_once(CLASSPATH . "solarium.class.php");
    require_once(SOLARIUM_PATH . 'init.php');
   
    $objSolarium    = new solarium();
    $oClient        = new Solarium\Client($config);
    
    $iPage          = 1;
    $article_limit  = 1;

    ## Set Endpoint
    $sEndpoint      = SOLR_ENDPOINT;
    $sUpStatus      = "";

    if(isset($_POST["form_submit"])=="true"){
        $sStateId               = trim($_POST["state_id"]);
        $sStateBio              = trim($_POST["description1"]);
        $sContent               = trim($_POST["description1"]);
        $sConstituencyId        = trim($_POST["const_id"]);
        $sCandidateId           = trim($_POST["c_id"]);
        $sConstituencyBio       = trim($_POST["description1"]);
        $sCandidateBio          = trim($_POST["description1"]);
        $sStatus                = trim($_POST["status"]);

        switch(trim($_POST["update_category"])){
            case 'state_bio':
                $objSolarium->UpdateConstituencyBio($oClient,"state",$sStateId,"","",$sStatus,"",$sStateBio);
                break;
            case 'state_constituency_bio':
                $objSolarium->UpdateConstituencyBio($oClient,"state_constituency_bio",$sStateId,"","",$sStatus,"",$sStateBio);
                break;
            case 'const_bio':
                $objSolarium->UpdateConstituencyBio($oClient,"constituency",$sStateId,$sConstituencyId,"",$sStatus,$sConstituencyBio,"");
                break;
            case 'state_quick_facts':
                $objSolarium->UpdateQuickFacts($oClient,"state_blob",$sStateId,"quick facts",$sStateBio);
                break;
            case 'candidate_bio':
                $objSolarium->UpdateConstituencyBio($oClient,"candidate",$sStateId,$sConstituencyId,$sCandidateId,$sStatus,$sCandidateBio,"");
                break;
            case 'candidate_order':
                $objSolarium->UpdateCandidateData($oClient,"order",$sCandidateId,$sStatus,$sCandidateBio);
                break;
            case 'candidate_slug':
                $objSolarium->UpdateCandidateData($oClient,"slug",$sCandidateId,$sStatus,$sCandidateBio);
                break;
            case 'candidate_blob':
                $objSolarium->UpdateQuickFacts($oClient,"candidate_blob",$sCandidateId,"quick facts",$sCandidateBio);
                break;
            case 'state_latest_update':
                print_r($_POST); die;
                //$objSolarium->UpdateCandidateData($oClient,"slug",$sCandidateId,$sStatus,$sCandidateBio);
                break;
            case 'const_latest_update':
                print_r($_POST); die;
                //$objSolarium->UpdateQuickFacts($oClient,"candidate_blob",$sCandidateId,"quick facts",$sCandidateBio);
                break;               
        }
    }

    $sQuery      = "category:constituency"; // if store is empty : fetch all data
    $iArtLimit   = 1;
    $sSort       = "state";
    $sOrder      = "asc";
    $sGroupField = "state";
    $aFields     = array("stateid","state");

    list($groups, $iNumRecords) = $objSolarium->getGroupSelect($oClient, $sQuery, $iPage, $iArtLimit, $sGroupField,$iGroupLimit =10,$aFields, $sSort, $sOrder,$sEndpoint); 

    list($resultset, $iNumRecords) = $objSolarium->getSimpleSelect($oClient, $sQuery, $iPage, $iArtLimit, $aFields, $sSort, $sOrder,$sEndpoint);
    if($iNumRecords >1){
        $iArtLimit  = $article_limit;
        list($resultset, $iNumRecords) = $objSolarium->getSimpleSelect($oClient, $sQuery, $iPage, $iArtLimit, $aFields, $sSort, $sOrder,$sEndpoint);
        $sURLCat    = "election_entity_list.php?page=";
        list($sPrevURL, $sNextURL, $sPGHTML) = $objSolarium->getPaginationPrevNext($iNumRecords, $iArtLimit, $iPage, $sURLCat, TRUE);
        $iTotalPages = $objSolarium->getTotalPages();
    }
    require_once( ADMINPATH . "admin_header.php");           
        
?>
     <style> .editImage, .editEntity{ padding: 15px; color:purple; }  </style> 

            <div id="form_container">
                <h1><a>Election Entity Edit</a></h1>
                <div>
                    <select class="element select small" id="stateid" name="stateid"> 
                        <option value="" selected="selected">--Select State--</option>
                            <?php 
                                foreach ($groups AS $groupKey => $fieldGroup) {
                                    foreach ($fieldGroup AS $valueGroup) {   
                                        foreach ($valueGroup AS $document) {
                                 ?>
                                            <option value="<?php echo $document->stateid;?>" ><?php echo ucwords($document->state);?></option>
                                <?php
                                        }                            
                                    }
                                }
                            ?>
                    </select>

                    <span  param="state_bio" class="editEntity" id="editEntity1"></span>
                    <span  param="state_image" class="editImage" id="editEntity4"></span>
                    <span  param="state_quick_facts" class="editEntity" id="editEntity2"></span> 
                    <span  param="state_constituency_bio" class="editEntity" id="editEntity3" ></span>
                    <span  param="state_latest_update" class="editEntity" id="editEntity5" ></span>
                    <span  param="state_alliance_result" class="editEntity" id="editEntity6" ></span>
                    <span  param="partywise_results" class="editEntity" id="editEntity7" ></span>
                </div>

                <br/>
                <label class="description">Select Constituency</label>
                <div>
                    <select class="element select small" id="constituencyid" name="constituencyid"> 
                        <option value="" selected="selected">--Constituency--</option>
                    </select>
                    <span  param="const_bio" class="editEntity" id="constituencyEntity1"></span>
                    <span  param="const_quick_facts" class="editEntity" id="constituencyEntity2"></span> 
                    <span  param="const_facts" class="editEntity" id="constituencyEntity3"></span>
                    <span  param="const_latest_update" class="editEntity" id="constituencyEntity4"></span>                    
                </div>
                <br/>
                <label class="description">List of Candidates</label>
                <div id="candidateid">
                    
                                         
                </div>

            </div>

            <div id="dialog" style="display: none" align="center">
              <form id="form_category" class="appnitro"  enctype="multipart/form-data" method="post" action="" onsubmit="return validate();">
                <input type="hidden" name="state_id" id="state_id" readonly="readonly" />
                <input type="hidden" name="const_id" id="const_id" readonly="readonly" />
                <input type="hidden" name="c_id" id="c_id" value="" readonly="readonly"/>
                                
                <label><strong id="dialog_heading"></strong></label><span style="float:right;background-color:#ff0;" id="final_msg"></span>
                <span id="editArea">
                    <div>
                        <textarea id="description1" name="description1" class="element textarea medium" rows="15" cols="80"></textarea>
                    </div>
                    <label class="description">Select Status </label>
                    <div>
                        <select class="element select small" id="status" name="status"> 
                            <option value="" selected="selected">-Select-</option>
                            <option value="active" >Active</option>
                            <option value="inactive" >Inactive</option>
                        </select>
                    </div>
                </span>   
                <p></p>
                <input type="hidden" name="update_category" id="update_category" value="" />
                <input type="hidden" name="form_submit" id="form_submit" value="false" />
                <input type="hidden" name="constituency" id="constituency" />
                <input id="saveForm" class="button_text" type="submit" name="submit" value="Submit" />
              </form>
            </div>
            <div id="dialog_image" style="display: none" align="center"></div>

        <script language="javascript" type="text/javascript">

            var admin_path = '<?php echo SITE_PATH ?>' ;
        
            tinymce.init({
                selector: "#description1",theme: "modern",width: 680,height: 200,
                plugins: [
                "advlist autolink link image lists charmap print preview hr anchor pagebreak",
                "searchreplace wordcount visualblocks visualchars insertdatetime media nonbreaking",
                "table contextmenu directionality emoticons paste textcolor responsivefilemanager code"
                ],
                toolbar1: "undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | styleselect",
                toolbar2: "| responsivefilemanager | link unlink anchor | forecolor backcolor  | code ",
                image_advtab: true ,

                filemanager_crossdomain: true,
                external_filemanager_path: admin_path + "responsive_filemanager/filemanager/",
                filemanager_title:"Responsive Filemanager" ,
                external_plugins: { "filemanager" : admin_path + "responsive_filemanager/filemanager/plugin.min.js"}
            });


            $("#stateid").change(function(){
                if( $("#stateid").val() != '' ){
                    $("#state").val( $("#stateid option:selected").text() );
                    $("#state_id").val( $("#stateid").val() );
                    $("#editEntity1").html("<?php echo " State Bio";?>");
                    $("#editEntity2").html("<?php echo " Quick Facts(Blob)";?>");
                    $("#editEntity3").html("<?php echo " State Constituency Bio";?>");
                    $("#editEntity4").html("Image");
                    $("#editEntity5").html("<?php echo " Latest Update";?>"); 
                    $("#editEntity6").html("<?php echo " Alliance Result";?>");
                    $("#editEntity7").html("<?php echo " Partywise Results";?>");
                }
                
                var stateId = $("#stateid").val();  
                var constituencyId = $("#constituencyid").val();
                var entityType = $("#category").val();  
                var url = "<?php echo SITE_PATH . "ajax.php"; ?>";
                var timestamp = new Date().getTime();
                $("#candidateid").html('');
                $.ajax({
                 url: url,
                 data: 'stateid='+stateId+'&type=constituency&v='+timestamp,
                 success: function(data){
                                if(data.length > 0){  
                                   // console.log(data);                        
                                    //tinyMCE.activeEditor.setContent(data);
                                    $("#constituencyid").html(data);
                                }
                         },
                         async:false
                });
            });


            $("#constituencyid").change(function(){
                if( $("#constituencyid").val() != '' ){
                    $("#const_id").val( $("#constituencyid").val() );
                    $("#constituencyEntity1").html("<?php echo " Constituency Biography";?>");
                    //$("#constituencyEntity2").html("<?php echo " Quick Facts";?>");
                    //$("#constituencyEntity3").html("<?php echo " Facts";?>");
                    $("#constituencyEntity4").html("<?php echo " Constituency Latest Updates";?>");
                
                    var stateId = $("#stateid").val();  
                    var constituencyId = $("#constituencyid").val();
                    var cslug = $('option:selected', this).attr('cslug');
                    var entityType = $("#category").val(); 
                    var url = "<?php echo SITE_PATH . "ajax.php"; ?>";
                    var timestamp = new Date().getTime();;
                    $("#candidateid").html('');
                    $.ajax({
                     url: url,
                     data: 'category=candidate&constituencyslug='+cslug+'&stateid='+stateId+'&v='+timestamp,
                     success: function(data){
                                    if(data.length > 0){  
                                       // console.log(data);                        
                                        $("#candidateid").html(data);
                                        $(".candidate_active").change(function(){
                                            var op = $(this).is(":checked");
                                            var key_value = op ? 1:0 ;
                                            var candidateId = $(this).attr("c_id" );
                                            //$objSolarium->UpdateCandidateData($oClient,"key",$sCandidateId,"",$sCandidateBio);
                                            var url1 = "<?php echo SITE_PATH . "ajax.php"; ?>";
                                            $.ajax({
                                             url: url1,
                                             data: 'category=candidate&field=key&key='+key_value+'&candidateid='+candidateId+'&v='+timestamp,
                                             success: function(data){
                                                            if(data.length > 0){  
                                                                alert("Key Candidates Updated");                               
                                                                   // alert(data); 
                                                            }
                                                     },
                                                async:false
                                            });

                                        });
                                        initialize();
                                    }
                             },
                        async:false
                    });
                }
            });

            $("#category_filter").change(function(){
                var category_filter = $("#category_filter").val();  
                if(category_filter==''){ 
                } else if  (category_filter =='all'){
                    var url = "<?php echo SITE_PATH . "election_entity_list.php"?>";
                    window.location =  url;
                } else {
                    var url = "<?php echo SITE_PATH . "election_entity_list.php?category="?>" + category_filter;
                    window.location =  url;
                }
            });

            function fetch_DB_content(){
                var stateId         = $("#stateid").val();
                var constituencyId  = $("#constituencyid").val();
                var cslug           = $('option:selected', this).attr('cslug');
                var candidateId     = $("#c_id").val();
                var entityType      = $("#category").val(); 
                var url             = "<?php echo SITE_PATH . "ajax.php"; ?>";
                var timestamp       = new Date().getTime();
                // console.log("state id="+stateId);
                // console.log("update_category : "+$("#update_category").val());
                var db_query        = '';
                var index ;
                switch($("#update_category").val()){
                    case 'state_bio' :
                        db_query    = 'stateid='+stateId+'&field=state_bio&type=constituency_bio&v=';
                        index       = 0;
                        break;
                    case 'state_constituency_bio':
                        db_query    = 'stateid='+stateId+'&type=constituency_bio&field=state_constituency_bio&v=';
                        index       = 2;
                        break;
                    case 'const_bio' :
                        db_query    = 'stateid='+stateId+'&constituencyid='+constituencyId+'&type=constituency_bio&v=';
                        index       = 1;
                        break;
                    case 'candidate_bio':
                        db_query    = 'category=candidate&constituencyid='+constituencyId+'&candidateid='+candidateId+'&stateid='+stateId+'&type=candidate_bio&v=';
                        index       = 2;
                        break;
                    case 'candidate_order':
                        db_query    = 'category=candidate&constituencyid='+constituencyId+'&candidateid='+candidateId+'&stateid='+stateId+'&type=candidate_bio&v=';
                        index       = 1;
                        break;
                    case 'state_quick_facts':
                        db_query    = 'id='+stateId+'&contenttype=blob&category=quick facts&entity=state&v=';
                        index       = 0;
                        break;
                    case 'candidate_blob':
                        db_query    = 'id='+candidateId+'&contenttype=blob&category=quick facts&entity=candidate&v=';
                        index       = 0;
                        break;
                    case 'candidate_slug':
                        db_query    = 'category=candidate&candidateid='+candidateId+'&type=candidate_bio&v=';
                        index       = 3;
                        break;
                    case 'state_alliance_result':
                        db_query    = 'category=state_alliance_result&action=get&stateId='+stateId+'&v=';
                        index       = 0;
                        break;
                    case 'partywise_results':
                        db_query    = 'category=partywise_results&action=get&stateId='+stateId+'&v=';
                        index       = 0;
                        break;
                    case 'state_latest_update':
                        db_query    = 'stateId='+stateId+'&category=state_latest_update&v=';
                        index       = 3; return ;
                        break;
                    case 'const_latest_update':
                        db_query    = 'stateId='+stateId+'&constituencyId='+constituencyId+'&type=const_latest_update&v=';
                        index       = 3; return ;
                        break;
                    default:
                        break;  
                }

                $.ajax({
                    url: url,
                    data : db_query+timestamp,
                    success: function(data){
                        if(data.length > 0) {
                            arrResult = data.split("##");
                            tinyMCE.get('description1').setContent(arrResult[index]);
                            if( arrResult[3] != " " ){
                                if(arrResult[3] == "active"){
                                    var stText = "Active";
                                } else {
                                    var stText = "Inactive";
                                }
                                $("select#status option").each(function() 
                                { this.selected = (this.text == stText); }); 
                            }                                   
                        }                                     
                    },
                    async:false
                });
            }

            function validate(){

                if($("#stateid").val() != ""){
                    var statename = $("#stateid option:selected").text();
                    $("#state").val(statename);   
                } else {
                    alert("Please select state");
                    $("#stateid").focus();
                    return false;
                }
                var tVar = $("#update_category").val();
                if( tVar == 'state_latest_update' || tVar == 'const_latest_update' ||  tVar == 'state_alliance_result' || tVar == 'partywise_results' ){
                    push_to_db();
                    return false;
                }
                $("#form_submit").val()="true";
            }

            function initialize(){
                $("#dialog").dialog({
                    modal: true,
                    autoOpen: false,
                    title: "Edit",
                    width: 800,
                    height: 550,
                   // close: function( ) { $( "#dialog" ).dialog( "destroy" ); initialize(); }
                });
                $("#dialog_image").dialog({
                    modal: true,
                    autoOpen: false,
                    title: "Edit",
                    width: 800,
                    height: 550,
                    //close: function( ) { $( "#dialog" ).dialog( "destroy" ); initialize(); }
                });
                $(".editEntity").unbind( "click" );
                $(".editEntity").click(function () {
                    tinyMCE.get('description1').setContent('',{format : 'raw'});
                    $("#update_category").val( $(this).attr("param" ) );
                    $('#dialog').dialog('open');
                    $("#dialog_heading").text( $(this).text() );
                    if($(this).attr("c_id" )){
                        $("#c_id").val( $(this).attr("c_id" ) );
                    }
                    fetch_DB_content();
                });
                $(".editImage").click(function () {
                    $('#dialog_image').dialog('open');
                    $("#dialog_heading").text( $(this).text() );

                    state_id_image = $("#stateid").val();
                    uniqid = $(this).attr("c_id" );
                    image_type = $(this).attr("param" );
                    turi   = admin_path + 'image_upload.php?uniqid=' + uniqid + '&image_type=' + image_type +'&state_id_image=' + state_id_image ;
                    $('#dialog_image').html('<iframe id="img_iframe" src="'+ turi +'" width="500" height="500"></iframe>');
                });

                $(".candidate_status").change(function(){
                    var candidateId     = $(this).attr("c_id" );
                    var candidateStatus = $('option:selected', this).val();
                    var url             = "<?php echo SITE_PATH . "ajax.php"; ?>";
                    var timestamp       = new Date().getTime();;
                    //alert( candidateId + ' '+ candidateStatus);
                    
                    $.ajax({
                     url: url,
                     data: 'category=candidate&field=status&candidateStatus='+candidateStatus+'&candidateid='+candidateId+'&v='+timestamp,
                     success: function(data){
                                    if(data.length > 0){
                                        alert("Status Updated" );
                                        //$(this).after("<b>Status Updated</b>");//.fadeOut( "slow" );;

                                    }
                             },
                        async:false
                    });
                });

            }
            initialize();

            
            function push_to_db(){
                var stateId         = $("#stateid").val();
                var constituencyId  = $("#constituencyid").val();
                var constituencyName= $('#constituencyid option:selected').text();
                var constituencySlug= $('#constituencyid option:selected').attr('cslug');
                var url             = "<?php echo SITE_PATH . "ajax.php"; ?>";
                var timestamp       = new Date().getTime();
                var db_query        = '';
                var textData        = tinyMCE.get('description1').getContent();
                if(textData =='' || textData == '<p></p>'){
                    alert("Please write some update !!!");
                    return false;
                }
                switch($("#update_category").val()){
                    case 'state_latest_update':
                        db_query    = 'category=state_latest_update&stateId='+stateId+'&textData='+escape(textData)+'&v=';
                        break;
                    case 'const_latest_update':
                        db_query    = 'category=const_latest_update&stateId='+stateId+'&constituencyId='+constituencyId+'&constituencySlug='+ constituencySlug +'&constituencyName='+ constituencyName  + '&textData='+ escape(textData) + '&v=';
                        break;
                    case 'state_alliance_result':
                        db_query    = 'category=state_alliance_result&stateId='+stateId + '&textData='+ escape(textData) + '&v=';
                        break;
                    case 'partywise_results':
                        db_query    = 'category=partywise_results&stateId='+stateId + '&textData='+ escape(textData) + '&v=';
                        break;
                    default:
                        break;  
                }

                $.ajax({
                    url: url,
                    data : db_query+timestamp,
                    success: function(data){
                        if(data.length > 0) {
                            if(data.length=='1'){
                                $('#final_msg').html("Data Updated");
                                if($("#update_category").val() == 'state_alliance_result' || $("#update_category").val() == 'partywise_results'){ }else{
                                    tinyMCE.get('description1').setContent('',{format : 'raw'});
                                }
                                $('#final_msg').fadeIn().delay(1000).fadeOut();                                 
                            } else {
                                alert("Errrrrr... Please contact Tech People..");
                            }
                        }                                     
                    },
                    async:false
                });

            }
</script>
             
<?php require_once( ADMINPATH . "admin_footer.php"); ?>        
