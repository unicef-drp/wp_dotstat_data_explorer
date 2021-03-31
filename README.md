# wp_dotstat_data_explorer

A wrapper for the OECD data explorer.
The plugin embeds a react application in the page and allows the interaction between the static PHP page and the React app.
## Known issues
The plugin has been adapted to work with a Fusion registry backend, there is some compatibility with the dotStat backend but it might require some structure's mapping to work properly.

## How to install
Copy the files in the Wordress' plugins directory

## Configuration
- Activate the plugin in the wordpress admin panel. The "Data Explorer menu will appear.
- Settings > Data Explorer: configure the remote url containing the configuration files.
- Data Explorers: Set the configuration ID assigned to the Data explorer instance (to be agreed with the Data Explorer's instance owner)