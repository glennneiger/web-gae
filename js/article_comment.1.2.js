$j(document).ready(function() {
       $j("#article-body a").each(function(i) {
               $j(this).addClass("lightsGal");
       });
       $j('a.lightsGal').zoomimage();
	
    $j("#text-size").mouseenter(
      function () {
	var y = $j("#text-size").offset().top;
	y = y - 170;
	$j("#tools").attr("style", "top:" + y + "px;"); 
        $j("#tools").fadeIn("slow");
      } 
    );

    $j("#tools").mouseleave(
      function () {
        $j("#tools").fadeOut("slow");
      } 
    );

    $j("#search-button").bind("click", function () { 
        var searchText = $j("#search").val();
        window.location="/library/search.htm?q=" + searchText;
    });

    $j("#stock-submit").bind("click", function() {
	var stock = $j("#stock-symbol").attr("value");
	window.location = "http://finance.minyanville.com/minyanville?Page=QUOTE&Ticker="+ stock+"&from=article";
    });

});

function showDisclaimer() {
        $j("#article-disclaimer").fadeIn("slow");
        $j("#disclaimer-link").html("<a href=\'javascript:\/\/\' onclick=\'hideDisclaimer();\'>Click here to hide the  disclaimer &gt;</a>");
}

function hideDisclaimer() {
        $j("#article-disclaimer").fadeOut("fast", function() {
                $j("#disclaimer-link").html("<a href=\'javascript:\/\/\' onclick=\'showDisclaimer();\'>Click here to read the disclaimer &gt;</a>");
         });
}


// commenting 
function stripHTML(oldString) {

   var newString = "";
   var inTag = false;
   for(var i = 0; i < oldString.length; i++) {
   
        if(oldString.charAt(i) == '<') inTag = true;
        if(oldString.charAt(i) == '>') {
              if(oldString.charAt(i+1)=="<")
              {
              		//dont do anything
	}
	else
	{
		inTag = false;
		i++;
	}
        }
   
        if(!inTag) newString += oldString.charAt(i);

   }
   return newString;
}

function postComment(frm,$type,badwords,conv_ids,strinbox) {
	   if(frm.id=='divPost'){
                frm='divPost';
        }
        if (badwords == "article-filter") {
                badwords = "ahole,anus,ash0le,ash0les,asholes,ass,Ass Monkey,Assface,assh0le,assh0lez,asshole,assholes,assholz,asswipe,azzhole,bassterds,bastard,bastards,bastardz,basterds,basterd,Biatch,bitch,bitches,Blow Job,boffing,butthole,buttwipe,c0ck,c0cks,c0k,Carpet Muncher,cawk,cawks,Clit,cnts,cntz,cock,cockbiter,cockbiters,cock-biter,cock-biters,cockhead,cock-head,cocks,CockSucker,cock-sucker,crap,cum,cunt,cunts,cuntz,damn,darn,dick,dild0,dild0s,dildo,dildos,dilld0,dilld0s,dominatricks,dominatrics,dominatrix,dyke,enema,f u c k,f u c k e r,fag,fag1t,faget,fagg1t,faggit,faggot,fagit,fags,fagz,faig,faigs,fart,flipping the bird,fuck,fucker,fuckin,fucking,fucks,Fudge Packer,fuk,Fukah,Fuken,fuker,Fukin,Fukk,Fukkah,Fukken,Fukker,Fukkin,g00k,gay,gayboy,gaygirl,gays,gayz,God-damned,h00r,h0ar,h0re,hells,hoar,hoor,hoore,jackoff,jap,japs,jerk-off,jisim,jiss,jizm,jizz,knob,knobs,knobz,kunt,kunts,kuntz,Lesbian,Lezzian,Lipshits,Lipshitz,masochist,masokist,massterbait,masstrbait,masstrbate,masterbaiter,masterbate,masterbates,Motha Fucker,Motha,Fuker,Motha Fukkah,Motha Fukker,Mother Fucker,Mother Fukah,Mother Fuker,Mother Fukkah,Mother Fukker,mother-fucker,Mutha Fucker,Mutha Fukah,Mutha Fuker,Mutha Fukkah,Mutha Fukker,n1gr,nastt ,nigger,nigur,niigerniigr,orafis,orgasim,orgasm,orgasum,oriface,orifice,orifiss,packi,packie,packy,paki,pakie,paky,pecker,peeenus,peeenusss,peenus,peinus,pen1s,penas,penis,penis-breath,penus,penuus,Phuc,Phuck,Phuk,Phuker,Phukker,polac,polack,polak,Poonani,pr1c,pr1ck,pr1k,pusse,pussee,pussy,puuke,puuker,queer,queers,queerz,qweers,qweerz,qweir,recktum,rectum,retard,sadist,scank,schlong,screwing,semen,sex,sexy,Sh!t,sh1t,sh1ter,sh1ts,sh1tter,sh1tz,shit,shits,shitter,Shitty,Shity,shitz,Shyt,Shyte,Shytty,Shyty,skanck,skank,skankee,skankey,skanks,Skanky,slut,sluts,Slutty,slutz,son-of-a-bitch,tit,turd,va1jina,vag1na,vagiina,vagina,vaj1na,vajina,vullva,vulva,w0p,wh00r,wh0re,whore,xrated,xxx";
        }
        str='txtTitle';
        var strtitle=document.getElementById(str).value;

        str='txtBody';
        var strbody=document.getElementById(str).value;

        var matchedbadwords="";
        var objWordLength=new Object();
        objWordLength.myParameter=false;
        // match bad words in subject
        matchedbadwords+=matchBadWords(badwords,strtitle,matchedbadwords,objWordLength,43);
        //match bad words in comment
        matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords,objWordLength,43);

        if(objWordLength.myParameter==true){
                alert('Some words are too long.');
                return false;
        }

        // match bad words in subject
        //matchedbadwords+=matchBadWords(badwords,strtitle,matchedbadwords);    
        //match bad words in comment
        //matchedbadwords=matchBadWords(badwords,strbody,matchedbadwords);

        //if bad words are found in posted words.
        if(matchedbadwords.length>0){
                alert('Following words are not allowed.\n'+matchedbadwords);
                return false;
        }
        if($type==0){
        
                if(strbody==''){
                        alert('Enter Comment');
                        return false;
                }

                str='subscription_id'+frm;
                var strsubscription_id=document.getElementById(str).value;

                str='thread_id'+frm;
                var strthread_id=document.getElementById(str).value;

                str='comment_id'+frm;
                var strcomment_id=document.getElementById(str).value;
/*      
        
                var url='Save.php';
                url=url+'?title='+encodeURIComponent(strtitle);
                url=url+'&post_text='+encodeURIComponent(strbody);
                url=url+'&poster_id='+strsubscription_id;       
                url=url+'&thread_id='+strthread_id;
                url=url+'&comment_id='+strcomment_id;
                preHttpRequest(frm,url);                
*/
                var url='title='+encodeURIComponent(stripHTML(strtitle));
                url=url+'&post_text='+encodeURIComponent(stripHTML(strbody));
                url=url+'&poster_id='+strsubscription_id;
                url=url+'&thread_id='+strthread_id;
                url=url+'&comment_id='+strcomment_id;
                url=url+'&Ptype=post';
                var save_url=host + '/articles/Save.php';
                preHttpRequestPost(frm,save_url,url);
				setTimeout("window.location.reload()",500);

        }
        else
        {
                if(strbody==''){
                        alert('Enter Message');
                        return false;
                }

                str='from_subscription_id'+frm;
                var strsubscription_id=document.getElementById(str).value;

                str='to_subscription_id'+frm;
                var strthread_id=document.getElementById(str).value;

                str='message_id'+frm;
                var strmessage_id=document.getElementById(str).value;

                var urlmessage='title='+encodeURIComponent(strtitle);
                urlmessage=urlmessage+'&from_subscription_id='+strsubscription_id;
                urlmessage=urlmessage+'&to_subscription_id='+strthread_id;
                urlmessage=urlmessage+'&private_msg_id='+strmessage_id;
                urlmessage=urlmessage+'&text='+encodeURIComponent(strbody);
                urlmessage=urlmessage+'&Ptype=sendmsg';
               urlmessage=urlmessage+'&strinbox='+strinbox;
                urlmessage=urlmessage+'&conv_ids='+conv_ids;
                if(strinbox){
                        var save_url= host + '/articles/Save.php';
                        preHttpRequestPostinbox(frm,save_url,urlmessage);
                    //var urlmessage='../articles/Save.php';
                } else {
                        var save_url= host + '/articles/Save.php';
                        preHttpRequestPost(frm,save_url,urlmessage);
                        //var urlmessage='Save.php';
                }
        }
}

function replyComment(subject) {
      var $target = $j("#comment-form");
      $target = $target.length && $target
      var targetOffset = ($target.offset().top - 20);
      $j('html,body').animate({scrollTop: targetOffset}, 1000);
      $j('.comment-form-title').attr('value', "RE: " + subject);

}

function deleteComment(frm,post_comment_id)
{
	if (confirm('Are you sure to delete this comment'))
	{
		var thread_id=document.getElementById('thread_id'+frm).value;	
		var data ="post_comment_id="+post_comment_id+"&thread_id="+thread_id;
		var url=host + '/articles/delete_comment.php'
		var myAjax4 = new Ajax.Request(url, {method: 'post',
			parameters: data,
			//onLoading:loading(erroDiv),
			//onLoading:$('loginErrorMsg').innerHTML="Loading...",
			onComplete:function()
			{						
				window.location.reload();				
			}
	   });
	}
}
