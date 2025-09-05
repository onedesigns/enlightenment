Enlightenment Theme
=======================

Requirements
------------

The project requires Homebrew, Yarn and Webpack to be installed locally. You can safely skip these steps if these are already installed on your system.

* Install Homebrew:

```bash
/usr/bin/ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
```

* Install Yarn:

```bash
brew install yarn --without-node
```

* Install Webpack:

```bash
yarn global add webpack
```

Getting Started
---------------

These steps are required after every fresh installation of the project, otherwise your static assets will not compile.

* Navigate to the local repository location:

```bash
cd /path/to/local/core
```

* Install all required dependencies:

```bash
yarn install
```

Editing Files
-------------

* To have a watch process active when editing SCSS or JS files:

```bash
yarn watch
```
