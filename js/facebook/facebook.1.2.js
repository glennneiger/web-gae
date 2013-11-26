var error;
function fbLogin(postvalue,fbId){
				action="autologin";
				postval=postvalue+"&fbId="+fbId+"&action="+action;                
				jQuery.ajax({
							type:"POST",
							url:host+"/lib/facebook/facebookhandler.php",
							data:postval,
							error:function(){
								jQuery('#signupError').html('Under Construction');
							},
							success:function(response){
								var result = eval('('+response+')');
								if(result.resultStatus=='true' || result.resultStatus=='1')
								{  
									/*error="";
									fbLoading(error);*/
									
									var tageturl=$F('targeturl');
									if(tageturl!='' && tageturl!=='undefined')
									{
										if(result.isRegister == '1'){
											googleVirtualPageTracking("/facebook-registration");
										}
										window.location.href= tageturl;	
									}else{
										if(result.isRegister == '1'){
											googleVirtualPageTracking("/facebook-registration");
										}
										window.location=host+'/subscription/register/controlPanel.htm';		
									}
									
								}else{
								    jQuery('#facebookerror').html(response);
									
								}								
							}
						});	
				
			}
			
var arFrnd = new Array();
            window.fbAsyncInit = function() {
				/* For QA */
                // FB.init({appId: '161531087208968', status: true, cookie: true, xfbml: true});
				/* For Production */
			 FB.init({
                                appId      : '139123259501339', // App ID
                                channelUrl : '//qa.minyanville.com/channel.html', // Channel File
                                status     : true, // check login status
                                cookie     : true, // enable cookies to allow the server to access the session
                                xfbml      : true  // parse XFBML
                              });
                /* All the events registered */
            
               /* FB.getLoginStatus(function(response) {
                    if (response.session) {
                        // logged in and connected user, someone you know
                       // login();
                       
                    }
                }); */
            };
            (function() {
                var e = document.createElement('script');
                e.type = 'text/javascript';
                e.src = document.location.protocol +
                    '//connect.facebook.net/en_US/all.js';
                e.async = true;
                document.getElementById('fb-root').appendChild(e);
            }());

         
            function login(){
                FB.login(function(response) {
				  if (response.authResponse) {
					error='Please wait...We Are Processing Your Request.';
					$('signupError').style.display="block";
					fbLoading(error);
					FB.api('/me', function(response) {
						fqlQuery();
					  // user is logged in and granted some permissions.
					  // perms is a comma separated list of granted permissions
					});
                                  }					
				   else {
					// user is not logged in
                                        /*
                                         error="Invalid Email/Password.";
						fbLoading(error);*/
					$('signupError').style.display="none";
					$('signupError').style.visibility = "hidden";
					
					fbLoading('');
				  }
				}, {perms:'email,status_update,publish_stream,read_stream,offline_access'})
            }
			
            			
			

            //stream publish method
            function streamPublish(name, description, hrefTitle, hrefLink, userPrompt, randomNumber, articleImage, badwords){
				var authorImage = articleImage;
				if(authorImage =="" || authorImage ==null ){
					authorImage = 'http://image.minyanville.com/images/logo.png';
				}
				
				var searchtext=hrefLink.search(/slideshow/i);
				if(searchtext>=1){
					if(description.length<1){
						str=$F('txtBody'+ randomNumber);
						var description = str;
					}
				}else{
					if(description.length<1){
						str=$F('txtBody');
						var description = str;
					}
				}
				 
				 if(description=="" || description==null){
					 alert("Enter a comment");
					 return false;
				 }
				 
				FB.ui(
                {
                    method: 'stream.publish',
                    message: '',
                    attachment: {
                        name: name+ ' commented on '+ hrefTitle,
                        caption: '{*actor*} wrote on Minyanville',
                        description: (description),
                        href: hrefLink,
						media: [{ 
							type: 'image', 
							src: authorImage,
							href: hrefLink
						 }]
                    },
					
                    action_links: [
                        { text: 'Start reading Minyanville now', href: host }
                    ],
                    user_prompt_message: userPrompt
                },
                function(response) {
					if(searchtext>=1){
						postHttpRequest(randomNumber,0,badwords);
					}else{
						postComment(randomNumber,0,'article-filter');
					}
                });

            }
            function showStream(){
                FB.api('/me', function(response) {
                    //console.log(response.id);
                    streamPublish(response.name, '', 'hrefTitle', 'http://qa.minyanville.com', "Share Post");
                });
            }

            function share(){
                var share = {
                    method: 'stream.share',
                    u: 'http://qa.minyanville.com'
                };

                FB.ui(share, function(response) { console.log(response); });
            }

            function graphStreamPublish(){
                var body = 'Reading New Graph api & Javascript Base FBConnect Tutorial';
                FB.api('/me/feed', 'post', { message: body }, function(response) {
                    if (!response || response.error) {
                        alert('Error occured');
                    } else {
                        alert('Post ID: ' + response.id);
                    }
                });
            }

            function fqlQuery(){
                FB.api('/me', function(response) {
                     var query = FB.Data.query('select first_name,uid,last_name,email,name from user where uid={0}', response.id);
                     query.wait(function(rows) {					
					
					fbId=rows[0].uid;
					followfrnd = '1';
					publish = '1';
							var postvalue = "firstname="+rows[0].first_name+"&lastname="+rows[0].last_name+"&uid="+rows[0].email+"&pwd="+Math.floor(1000000*Math.random())+"&fbId="+fbId+"&dailydigest=1"+"&follow_frnds="+followfrnd+"&publish_post="+publish;
					//getFBfriendList(fbId);		
					if(postvalue!=""){
						fbLogin(postvalue,fbId);	
					}
							
                     });
				});
            }
			
			
			
			function fbCheckUser(postvalue,fbId){
				action="exists";
				postval="fbId="+fbId+"&action="+action;
				jQuery.ajax({
							type:"POST",
							url:host+"/lib/facebook/facebookhandler.php",
							data:postval,
							error:function(){
								jQuery('#regerror').html('Under Construction');
							},
							success:function(response){
								if(response=='true')
								{
									fbLogin(postvalue,fbId);
								}
								else
								{
									fbRegister(postvalue,fbId);
								}								
							}
						});	
				
			}
			
			
		
			function fbInsert(postvalue,fbId){
				var frndlist = arFrnd.join(',');  
				action="fbinsert";
				postval=postvalue+"&action="+action+"&frndlist="+frndlist;
				jQuery.ajax({
							type:"POST",
							url:host+"/lib/facebook/facebookhandler.php",
							data:postval,
							error:function(){
								jQuery('#regerror').html('Under Construction');
							},
							success:function(response){
								if(response=='true')
								{
									googleVirtualPageTracking("/facebook-registration");
									window.location=host+'/subscription/register/controlPanel.htm';	
								}
								else
								{
									jQuery('#facebookerror').html(response);
								}								
							}
						});	
				
		   }
			
			
			function fbRegister(postvalue,fbId){
				action='fbregisteration';
				postval="postvalue="+postvalue+"fbId="+fbId+"&action="+action;
				jQuery.ajax({
							type:"POST",
							url:host+"/lib/facebook/facebookhandler.php",
							data:postvalue,
							error:function(){
								jQuery('#regerror').html('Under Construction');
							},
							success:function(response){
								var data = eval('('+response+')');
								if(data.status==true)
								{
									fbInsert(postvalue,fbId);
								}
								else
								{
								jQuery('#regerror').html(data.msg);
								}								
							}
						});	
				
			}
			
			function getFBfriendList(fUserId){
			var friends = FB.Data.query("SELECT name, uid FROM user WHERE uid IN (SELECT uid2 FROM friend WHERE uid1={0}) ORDER BY name", parseInt(fUserId));
			friends.wait(function(rows){
			for(i=0;i<rows.length;i++)
				{
				  arFrnd[i] = rows[i].uid; 
				} 
				 console.log(rows);
			});

			 
		    }
			

            function setStatus(){
                status1 = document.getElementById('status').value;
                FB.api(
                  {
                    method: 'status.set',
                    status: status1
                  },
                  function(response) {
                    if (response == 0){
                        alert('Your facebook status not updated. Give Status Update Permission.');
                    }
                    else{
                        alert('Your facebook status updated');
                    }
                  }
                );
            }
			
			
	function fbLoading(error){
		$("signupError").style.display='block';
		jQuery('#signupError').html(error); 
	}
			