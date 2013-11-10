;NSIS Modern User Interface version 1.68
;Basic Example Script
;Written by Joost Verburg

;--------------------------------
;Include Modern UI

  !include "MUI.nsh"

;--------------------------------
;Configuration

  ;General
  Name "Minyanville Alert Application"
  OutFile "mv_installer.exe"

  ;Folder selection page
  InstallDir "$PROGRAMFILES\Minyanville"
  
  ;Get install folder from registry if available
  InstallDirRegKey HKCU "Software\Minyanville" ""


;--------------------------------
;Variables

  Var MUI_TEMP
  Var STARTMENU_FOLDER

;--------------------------------

;Interface Settings

  !define MUI_ABORTWARNING


;--------------------------------
;Pages

  !insertmacro MUI_PAGE_LICENSE "${NSISDIR}\Examples\Modern UI\mv-alert\License.txt"
     
  ;Start Menu Folder Page Configuration
  !define MUI_STARTMENUPAGE_REGISTRY_ROOT "HKCU" 
  !define MUI_STARTMENUPAGE_REGISTRY_KEY "Software\Minyanville" 
  !define MUI_STARTMENUPAGE_REGISTRY_VALUENAME "Minyanville"  
  !insertmacro MUI_PAGE_STARTMENU Application $STARTMENU_FOLDER


  !insertmacro MUI_PAGE_DIRECTORY
  !insertmacro MUI_PAGE_INSTFILES  
  !insertmacro MUI_UNPAGE_CONFIRM
  !insertmacro MUI_UNPAGE_INSTFILES

  
;--------------------------------
;Languages
 
  !insertmacro MUI_LANGUAGE "English"

;--------------------------------
;Installer Sections

Section "Dummy Section" SecDummy

  SetOutPath "$INSTDIR"
  
  ;ADD YOUR OWN STUFF HERE!
  
  StrCpy $MUI_TEMP "Minyanville"
  StrCpy $STARTMENU_FOLDER $MUI_TEMP

  WriteRegStr HKCU "Software\$STARTMENU_FOLDER" "" $INSTDIR
  
  ;Create uninstaller
  WriteUninstaller "$INSTDIR\Uninstall.exe"


 !insertmacro MUI_STARTMENU_WRITE_BEGIN Application 
    ;Create shortcuts
    CreateDirectory "$SMPROGRAMS\$STARTMENU_FOLDER"
    CreateShortCut "$SMPROGRAMS\$STARTMENU_FOLDER\Uninstall.lnk" "$INSTDIR\Uninstall.exe"
    CreateshortCut "$SMPROGRAMS\$STARTMENU_FOLDER\Minyanville.lnk" "$INSTDIR\mv-alert.exe"
    CreateshortCut "$SMSTARTUP\Minyanville.lnk" "$INSTDIR\mv-alert.exe"
    CreateshortCut "$DESKTOP\Minyanville.lnk" "$INSTDIR\mv-alert.exe"
 !insertmacro MUI_STARTMENU_WRITE_END

    File /r "${NSISDIR}\Examples\Modern UI\mv-alert\alert\mv-alert\*.*"


SectionEnd

 
;--------------------------------
;Uninstaller Section

Section "Uninstall"

  Delete "$INSTDIR\Uninstall.exe"

  RMDir "$INSTDIR"
  
  !insertmacro MUI_STARTMENU_GETFOLDER Application $MUI_TEMP
  
  RmDir  /r "$SMPROGRAMS\Minyanville"
  Delete "$SMSTARTUP\Minyanville.lnk"
  Delete "$DESKTOP\Minyanville.lnk"
  RmDir  /r "$INSTDIR"




SectionEnd
