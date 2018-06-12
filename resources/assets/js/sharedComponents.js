/**
 * Central registration of all Vue components
 * 
 * (
 *  see https: //github.com/chrisvfritz/7-secret-patterns
 * and https: //www.youtube.com/watch?v=7lpemgMhi0k
 * )
 *
 * (C) 2018 Matthias Kuhs
 */
import Vue from 'vue'
import upperFirst from 'lodash/upperFirst'
import camelCase from 'lodash/camelCase'

// Get a list of all components
const requireComponent = require.context(
  './components', true, /^.*\.vue$/
)

export default function sharedComponents() {
  requireComponent.keys().forEach(filename => {
    // Get component config
    const componentConfig = requireComponent(filename)

    // Get PascalCase name of component
    const componentName = upperFirst(
      camelCase(filename.replace(/^\.\//, '').replace(/\.\w+$/, ''))
    )

    // Register each component globally
    window.console.log('Registering Vue component -', componentName)
    Vue.component(componentName, componentConfig.default || componentConfig)
  })
}
