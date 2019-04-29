# Nova-AdvancedStats
An extension for Anodyne's Nova that adds advanced statistics about the sim

# Installation
1. Download the extension files from the [latest release](https://github.com/mooeypoo/Nova-AdvancedStats/releases)
2. Place the extension files in `nova/application/extensions/AdvancedStats`
3. If you're using [ExtensionManager](https://github.com/mooeypoo/Nova-ExtensionManager), go to your Control Panel, click on "Extension Management" and enable the AdvancedStats extension.
4. If you're not using [ExtensionManager](https://github.com/mooeypoo/Nova-ExtensionManager), paste the following code into `nova/config/extensions.php`:
```
$config['extensions']['enabled'][] = 'AdvancedStats';
```

# Usage

After installation, you should see "Advanced Stats" under "Reports" in your administration menu.
This menu is available for anyone that is allowed to see general reports on Nova.

**NOTE:** While the extension does have some user defined settings, they are all controlled through the interface at the statistics page itself. Please do not change those values directly. Use the "Settings" button in "Adcanced Settings" page.

## Bugs and feature requests
Please submit bugs or feature requests as issues to this repository.
