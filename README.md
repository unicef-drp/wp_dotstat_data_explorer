# wp_dotstat_data_explorer

A wrapper for the OECD data explorer.
The plugin embeds a react application in the page and allwos the interaction between the static PHP page and the React app.
## Known issues
The plugin has been adapted to work with a Fusion registry backend, there is some compatibility with the dotStat backend but it might require some structure's mapping to work properly.

## How to install
Copy the files in the Wordress' plugins directory

## Configuration
- Activate the plugin in the wordpress admin panel. The "Data Explorer menu will appear.
- Create a new data explorer, the params are:
  - Page title: the title of the page
  - API URL the SDMX endpoint to connect to (see the example in wordpress)
  - Backend: Fusion or dotStat registry?
  - Hierarchy: can show a regional hierarchy (see the example in wordpress)
  - Forced dims (experimental): can force a dimension to take a fixed value and be hidden to the user.

