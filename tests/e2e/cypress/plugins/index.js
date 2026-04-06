const fs = require('fs-extra')
const path = require('path')

const configHandler = {
    getConfigurationByFile: (file) => {
        const pathToConfigFile = path.resolve('config', `${file}.json`)

        return fs.readJson(pathToConfigFile)
    },
    getBaseCypressConfig: () => {
        const pathToCypress = path.resolve('cypress.json')
        return fs.readJson(pathToCypress)
    }
}

module.exports = configHandler
