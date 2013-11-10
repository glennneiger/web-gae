<?php
global $HTPFX,$HTHOST;
 $Minyanville_logo_image = "<a href='".$HTPFX.$HTHOST."'><img src='".$IMG_SERVER."/images/redesign/spacer.gif' alt='Minyanville Logo' width='253px' height='107px'/></a>";
 $emailId = $_REQUEST['email'];

?>
<link href="<?=$HTPFX.$HTHOST?>/css/minyanville.1.52.css" rel="stylesheet" type="text/css" />
<script src="<?=$HTPFX.$HTHOST?>/js/config.1.2.js" type="text/javascript"></script>
<script src="<?=$HTPFX.$HTHOST?>/js/min/prototype.1.4-min.js" type="text/javascript"></script>
<script src="<?=$HTPFX.$HTHOST?>/js/min/prototype.1.4-min.js" type="text/javascript"></script>
<script src="<?=$HTPFX.$HTHOST?>/js/ibox_registration.1.20.js" type="text/javascript"></script>
<script src="<?=$HTPFX.$HTHOST?>/js/registration_ajax_1.20.js" type="text/javascript"></script>
<script src="<?=$HTPFX.$HTHOST?>/js/redesign.1.26.js" type="text/javascript"></script>
<div class="mv_logo"><?=$Minyanville_logo_image ?></div>
 <div id="Registrationstep1" class="1st_step_container">
		<div class="register_title_first"><span id="regstep1" class="erordiv"></span><span id="statusmsgstep1" class="erordiv"></span></div>
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
		  <tr>
		  <td width="28%" align="right" valign="top">First Name</td>
		  <td><label><?php input_wordsonly("firstname","",0,255,"class=register_common_input onKeyPress=get_register_key_Scott(event); tabindex=211"); ?></label></td>
		  <td width="41%" rowspan="5" valign="top">
			
		  </td>
		  </tr>
		  <tr>
		  <td width="23%"align="right" valign="top">Last Name</td>
		  <td><label><?php input_wordsonly("lastname","",0,255,"class=register_common_input onKeyPress=get_register_key_Scott(event); TABINDEX=212"); ?></label></td>
		  </tr>
		  <tr>
		  <td width="23%" align="right">Email Address (username) </td><td width="36%"><label><?php input_email("viauserid","$emailId",0,0,"class=register_common_input onKeyPress=get_register_key_Scott(event); TABINDEX=213"); ?></label></td>
		  </tr>
		  <tr>
		  <td align="right">Password</td>
		  <td><?php input_pass("viapass","",20,255,"class=register_common_input onKeyPress=get_register_key_Scott(event); TABINDEX=214"); ?></td>
		  </tr>
		<!--  <tr>
		  <td align="right">Re-enter password </td>
		  <td><?php input_pass("viarepass","",20,255,"class=register_common_input onKeyPress=get_register_key_Scott(event); TABINDEX=215"); ?></td>
		  </tr>
		   <tr>
		  <td align="right" valign="top"><?php input_check("viauserremember",1,"tabindex=216"); ?></td>
		  <td><label>Remember my Member ID and Password at this computer </label></td>
		  </tr> -->
		  <tr>
		  <td colspan="3">
			<table width="100%" border="1" cellspacing="0" cellpadding="6">
			
			<!-- <tr>
			<td width="5%" valign="top"><label><?php input_check("alerts",1,"tabindex=217"); ?></label></td>
			<td colspan="2"><div class="register_head">Minyanville Digest</div> Yes, I'd  like a daily email summary of the articles published on Minyanville.com </td>
			</tr> -->
			<!-- <tr>
			<td><?php input_check("terms",0,"tabindex=218"); ?></td>
			<td><label>I hereby accept Minyanville's <span class="blue_link"><a onclick="window.open('<?=$HTPFX.$HTHOST?>/company/substerms.htm','terms','width=560,height=500,resizable=1,scrollbars=1')" href="#">Terms of Use</a></span> </label></td>
				
			</tr> -->
		  </table>
		  </td>
		  </tr>
		  <tr>
			<td align="right"><?php input_check("terms",0,"tabindex=218"); ?></td>
			<td><label>I hereby accept Minyanville's <span class="blue_link"><a onclick="window.open('<?=$HTPFX.$HTHOST?>/company/substerms.htm','terms','width=560,height=500,resizable=1,scrollbars=1')" href="#">Terms of Use</a></span> </label></td>
				
			</tr>
		  <tr><td align="right"><img tabindex="219" onKeyPress="get_register_key_Scott(event);" style="cursor:pointer;" src="<?=$HTPFX.$HTHOST?>/images/redesign/create_account.jpg" onclick="javascript:validateScottBuzz('statusmsgstep1');" /></td>
		  <td ><img onclick="javascript:banterWindow=window.open('/buzz/buzz.php','Banter','width=350,height=708,resizable=yes,toolbar=no,scrollbars=no');banterWindow.focus();" alt="Sign in to the Buzz & Banter" src="<?=$IMG_SERVER;?>/images/redesign/launch_buzz_home.gif" /></td>
		  
		  </tr>
		</table>
	</div>
