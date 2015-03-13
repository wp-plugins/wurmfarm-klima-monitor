=== Wurmfarm Klima Monitor ===
Tags: Raspberry Pi, GrovePi+, Wurmfarm, Temperatur, Luftfeuchtigkeit, Luftdruck, Bodenfeuchtigkeit
Contributors: mayerst
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: 1.1.0
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dieses Plugin ermöglicht die Darstellung von Temperatur, Luftfeuchtigkeit, Luftdruck, Bodenfeuchtigkeit.

== Description ==

Dieses Plugin ermöglicht die Darstellung von Temperatur, Luftfeuchtigkeit, Luftdruck, Bodenfeuchtigkeit, welche mit Sensoren am GrovePi+ gemessen und mit RaspberryPi verarbeitet wurden.
Es liest aus der Wordpress Datenbank die Daten und zeigt diese in einem Liniendiagramm an.  

== Installation ==

1. Installiere das Plugin in */wp-content/plugins Verzeichnis
2. Aktiviere das Plugin im "Plugin" Menü von Wordpress, es wird automatisch eine neue Tabelle xxx_climadata angelegt. 
	(xxx = Wordpress prefix
3. Durch drücken der Wurm-Icone (auf Seiten und Beiträgen), wird ein Shortcode für ein Liniendiagramm erzeugt:
   [ws_chart title="Heute" chart="temp" day="Today" v_title="Temperatur" width="800px" height="400px" ]
4. Ändere den erzeugten Code nach Deinen Wünschen ab

		
== Frequently Asked Questions ==

= Was benötige ich für dieses Plugin? =

Du benötigst einen Raspberry Pi, ein GrovePi+ Shield, Temperatur-, Luftdruck- und Bodenfeuchtigkeitssensor.
Ebenso benötigst Du das Coding, mit welchem die Daten in die Datenbank geschrieben werden.
Für mehr Informationen, besuche bitte die Projektseite http://www.2komma5.org 

= An wen kann ich mich wenden, wenn ich Fragen habe? =

Meine eMail Adresse lautet: info@2komma5.org

== Screenshots ==

1. Wochenübersicht der Temperatur

2. Wochenübersicht des Luftdrucks

== Changelog ==

= 1.0.0 =
* Initial release

= 1.1.0 =
* Im Bereich Einstellungen-Allgemein wird ein Flag eingeführt, welches das Löschen der Datenbanktabelle steuert
* Anzeige der Datenbanktabellen-Version, Datenbanktabellen-Name und Plugin Version im Bereich Einstellungen-Allgemein
* Bessere Strukturierung des Codings

== Einstellmöglichkeiten ==

title   - Definition des Titels z.B.: title="Dies ist ein Titel"

chart 	- Definition der Anzeige, "temp" oder "press" z.B.: char="temp" 

day   	- Definition des Anzeigebereichs, "Today", "Yesterday", "Week", "Month", "Year" 
          z.B.: day="Week" Anzeige der Daten der letzten 7 Tage

v_title - Definition der y-Achsen Beschriftung

Im Bereich Einstellungen-Allgemein steuert ein Flag, das mögliche Löschen der Datenbanktabelle beim Deaktivieren