<?php // Klasse, die die Funktionalität für eine Einstellungsseite bereitstellt.// Angelehnt an: https://codex.wordpress.org/Creating_Options_Pagesclass SettingsPage{    const sMENU_TITLE = 'ct-cal';    const sPAGE_NAME = 'ct-cal-settings';    const sPAGE_TITLE = 'Einstellungen für die ChurchTools-Kalender-Anbindung';    const sOPTION_GROUP = 'ct-cal-options';        const sSECTION_NAME = 'ct-cal-section';    const sSECTION_TITLE = 'ChurchTools-Anbindung';        const sADDRESS_TITLE = 'Adresse von ChurchTools';        const sCATEGORY_TITLE = 'Datenbankkennungen der Kalender (kommasepariert)';        const sPERIOD_TITLE = 'Abfragezeitraum (in Tagen)';    const sINTERVAL_TITLE = 'Aktualisierungsintervall (in Sekunden)';        /**     * Holds the values to be used in the fields callbacks     */    private $aOptions;    // Konstruktor - Ruft die benötigten WordPress-Funktionen auf    public function __construct()    {        add_action('admin_menu',array($this,'addPage'));        add_action('admin_init',array($this,'initPage'));    }    // Fügt einen Menüpunkt hinzu und definiert die neue Seite    public function addPage()    {        if (FALSE)        {            // Eigener Menüpunkt            add_menu_page(self::sPAGE_TITLE,self::sMENU_TITLE,'manage_options',self::sPAGE_NAME,array($this,'createPage'));        }        else        {            // Menüpunkt unter "Einstellungen"            add_options_page(self::sPAGE_TITLE,self::sMENU_TITLE,'manage_options',self::sPAGE_NAME,array($this,'createPage'));        }    }    // Liefert den Seiteninhalt    public function createPage()    {        // Set class property        $this->options = get_option(sOPTION_NAME);        ?>        <div class="wrap">            <h1><?php echo(self::sPAGE_TITLE); ?></h1>            <form method="post" action="options.php">            <?php                settings_fields(self::sOPTION_GROUP);                do_settings_sections(self::sPAGE_NAME);                submit_button();            ?>            </form>        </div>        <?php    }    // Initialisiert die Seite.    public function initPage()    {                register_setting(self::sOPTION_GROUP,sOPTION_NAME,array($this,'sanitize'));        $aSections = array(self::sSECTION_TITLE.' 1',self::sSECTION_TITLE.' 2',self::sSECTION_TITLE.' 3');                    $aTitles = array(self::sADDRESS_TITLE,self::sCATEGORY_TITLE,self::sPERIOD_TITLE,self::sINTERVAL_TITLE);        $aNames = array(sADDRESS_NAME,sCATEGORY_NAME,sPERIOD_NAME,sINTERVAL_NAME);        $aDefaults = array(sADDRESS_DEFAULT,sCATEGORY_DEFAULT,sPERIOD_DEFAULT,sINTERVAL_DEFAULT);                for ($i = 1;$i <= 3;$i++)        {            delete_transient('churchtools_calendar'.$i); // Aufruf der Seite löscht die Speicher            add_settings_section(self::sSECTION_NAME.$i,$aSections[$i - 1],array($this,'createInformation'.$i),self::sPAGE_NAME);              $aCallback = array($this,'createInput');                        for ($j = 0;$j < 4;$j++)            {                if (($i == 1) || ($j > 1))                {                    $aArgs = array('name'=>$aNames[$j].$i,'default' => $aDefaults[$j]);                }                else                {                    $aArgs = array('name'=>$aNames[$j].$i,'default' => '');                }                add_settings_field($aNames[$j],$aTitles[$j],$aCallback,self::sPAGE_NAME,self::sSECTION_NAME.$i,$aArgs);             }        }    }    // Setzt die Variablen.    public function sanitize($aInput)    {        $aOutput = array();        $aNames = array(sADDRESS_NAME,sCATEGORY_NAME,sPERIOD_NAME,sINTERVAL_NAME);        for ($i = 1;$i <= 3;$i++)        {            for ($j = 0;$j < 4;$j++)            {                $sName = $aNames[$j].$i;                                if (isset($aInput[$sName]))                {                    $aOutput[$sName] = sanitize_text_field($aInput[$sName]);                }            }        }        return $aOutput;    }    // Liefert einen zusätzlichen Text für einen Abschnitt    public function createInformation1()    {        print('Shortcode: [ct-cal id=1 ...]...[/ct-cal]');    }    public function createInformation2()    {        print('Shortcode: [ct-cal id=2 ...]...[/ct-cal]');    }    public function createInformation3()    {        print('Shortcode: [ct-cal id=3 ...]...[/ct-cal]');    }        // Liefert das Eingabefeld    public function createInput($aArgs)    {        $sName = $aArgs['name'];        $sDefault = $aArgs['default'];                printf(            '<input type="text" id="'.$sName.'" name="'.sOPTION_NAME.'['.$sName.']" value="%s" />',            isset($this->options[$sName]) ? esc_attr( $this->options[$sName]) : $sDefault        );    }}////////////////////////////////////////////////////////////////////////////////?>