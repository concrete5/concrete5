# Building concrete5 Assets

The concrete5 version currently available on GitHub already contains the required CSS and JavaScript assets.
If you want to modify these assets you'll need to rebuild them. 

## Requirements
In order to build assets for concrete5 you need:
- [Node.js](http://nodejs.org/)
- [npm](http://npmjs.org/) (may be bundled with Node.js)

Once you have node.js and npm, you have to install the [grunt](http://gruntjs.com/) client.
You can install it globally with `npm install -g grunt-cli`. This requires that you may need to use sudo (for OS X, \*nix, BSD, …) or run your command shell as Administrator (for Windows).
If you don't have administrator rights, you may need to install the grunt client locally to your project using `npm install grunt-cli`.
Unfortunately, this will not put the grunt executable in your PATH, so you'll need to specify its explicit location when executing it (for OS X, \*nix, BSD `node_modules/.bin/grunt`, for Windows `node_modules\.bin\grunt`).

Once you have installed the grunt client, you need to install the project dependencies: simply launch the following command: `npm install` from inside the `build` directory.
Note that you will need to install third party PHP dependencies via Composer if you have not done so already.

### Task parameters

Every task may have its own parameters. These parameters may be specified inline or in a file called `Gruntfile.parameters.js`.

For instance, you may execute this line of code:
`grunt taskname --nameOfTheOption=valueOfTheOption`.

Or you can have a  `Gruntfile.parameters.js` like this:
```
module.exports = {
	nameOfTheOption: 'valueOfTheOption'
};
```
And you can simply launch `grunt taskname`.

## Building .css files

- For debugging: `grunt css:debug`
	- The generated .css files are more readable, but they have a slightly bigger size.
- For production: `grunt css:release` (or simply `grunt css`)
	-  The generated .css files are slightly smaller, but they are harder to read from a human point of view.


## Building .js files

- For debugging: `grunt js:debug`
	- Building for debug will generate JavaScript SourceMap files, that greatly improve the debugging of scripts with modern browsers.
- For production: `grunt js:release` (or simply `grunt js`)
	- SourceMap files won't be generated.  

## Building with Grunt Watch & Livereload

- Running `grunt watch` from this directory and saving `.less` and `.js` files with trigger the `css:debug` and `js:debug` tasks.
- Install the Livereload plugins for [Firefox](https://addons.mozilla.org/en-US/firefox/addon/livereload/) and [Chrome](https://chrome.google.com/webstore/detail/livereload/jnihajbhpnppcggbcgedagnkighmdlei) in order to have the browser automatically refresh after build completes.

## Building everything

- For debugging: `grunt debug`
	- it's the same as calling `grunt css:debug` and `grunt js:debug`.
- For production: `grunt release` (or simply `grunt`)
	- it's the same as calling `grunt css:release` and `grunt js:release`.


## Built assets and GIT

Since the CSS and JavaScript assets are under GIT, you may want to tell GIT to temporarily ignore these assets in your working copy of the repository.
This can be done with this command: `grunt gitskip-on`.
To let GIT reconsider your local assets, simply run `grunt gitskip-off`.


## Debugging JavaScript with source maps

If you have installed concrete5 in a sub-directory and you want to debug JavaScript with SourceMaps, you should update the `Gruntfile.js` file, changing the following line:
`config.DIR_REL = '';`
For instance, if your concrete5 installation is at http://www.domain.com/c5subfolder, you should change it to:
`config.DIR_REL = '/c5subfolder';`


## Downloading translations from Community Translation

To download translations from a Community Translation server, you can use the `translations` task.
You have to specify the following parameters:
- `ctEntryPoint` the entry point URI of the API of the Community Translation server.
- `ctAPIToken` the API Token to be used to authenticate yourself on the Community Translation server (may not be required).
- `ctPackage` the handle of the package on the Community Translation server.
- `ctPackageVersion` the version of the package on the Community Translation server.
- `ctLocales` a comma-separated list of locale identifiers (for instance: `de_DE,it_IT,el_GE`) that you want to download. If this option is specified in the `Gruntfile.parameters.js` file you can also write a Javascript array (for instance: `module.exports.ctLocales = ['de_DE' ,'it_IT', 'el_GE'];`). If you don't specify this value then all the available locales will be fetched.
- `ctProgressLimit` the task will retrieve only translations above this limit. For instance, if you specify `90`, then the task will download translations that are at least at 90% (_the default value for this option is 95%_).

Examples:
```Shell
grunt translations --ctEntryPoint=https://translate.concrete5.org/api --ctPackage=concrete5 --ctPackageVersion=dev-8 --ctLocales=de_DE,it_IT,el_GR

grunt translations --ctEntryPoint=https://translate.concrete5.org/api --ctPackage=concrete5 --ctPackageVersion=dev-8 --ctProgressLimit=90
```
As stated above, some or all of these options can also be specified in the `Gruntfile.parameters.js` file (but the command-line options take the precedence).


## Remove short tags

You can use the `remove-short-tags` grunt task.
It accepts the following parameters (in `Gruntfile.parameters.js` or in command line):
- `package` The package handle you want to work on. If not specified (or if its value is `-`) the task work on the whole concrete5 directory. 
- `destination` The destination path. If not specified the source files will be overwritten.
- `shortTagRemover` A shell command that will replace short tags with long tags for a specific file. It must accept two syntaxes:
	- `command filename`: the command should overwrite the specified file
	- `command sourceFilename destinationFilename`: the command should read from `sourceFilename` and save to `destinationFilename`

If the `shortTagRemover` is not specified, the task uses a predefined PHP script (so your system should have PHP installed, minimum required version 5.3.9).

Example: `grunt remove-short-tags --package=your_package_handle --destination=../out`
