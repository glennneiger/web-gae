<?

function Before_UnsubscribeDesign() {
?>
<table width="100%" cellspacing="0" cellpadding="3" border="0" align="left">
     <tr>
        <td width=30% align="left"><input type="text" name="emailadd" id="emailadd" onfocus="javascript:if(this.value=='Enter Email Address') this.value=''; return false;" onblur="javascript:chkSpaceNull('emailadd',this.value,'Enter Email Address');" value="Enter Email Address" class="login_input_box" tabindex="49" /></td>
      <td width=70% align="left">
        <input type="image" src="<?=$IMG_SERVER?>/images/redesign/unsubscribe.jpg" name="unsubscribe" id="unsubscribe" width="99" height="28" onClick="return iboxUnsubscribe('emailadd','unsubscribeErrorMsgs');">
       </td>
      </tr>
    <tr>

        </tr>
     </table>

<?
}

function After_UnsubscribeDesign() {
?>

<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" class="login_table">
     <tr>
        <td align="left" class="unsubscribe_content">You have been Unsubscribed from Minyanville Newsletters.</td>
     </tr>

 </table>

<?
}

function email_content() {
global $HTHOST, $HTPFX, $HTADMINHOST;
?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
<tr>
     	<td align="left" class="unsubscribe_content">To unsubscribe from Minyanville emails, enter your email address below and click unsubscribe.  You will be unsubscribing from all free emails from Minyanville.  If you'd only like to unsubscribe to a certain email, please click <a href="<?$HTPFXSSL.$HTADMINHOST;?>/subscription/register/controlPanel.htm">Manage Email Settings</a> and make the appropriate changes.
     	</td>
     </tr>
     <!--<tr><td>&nbsp;</td></tr>
     <tr>
     	<td align="left" class="unsubscribe_content">Once you enter your email, a verification email will be generated to the email address specified.  Simply click the link in that email and you will be unsubscribed.
     	</td>
     </tr>-->
   </table>
<?
}

?>
