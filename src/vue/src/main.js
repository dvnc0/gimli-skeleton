import { createApp } from 'vue'

const modules = import.meta.glob('./components/*.vue', { eager: true })
const components = {}
for (const path in modules) {
  components[modules[path].default.__name] = modules[path].default
}

for (const el of document.getElementsByClassName('vue-app')) {
  const app = createApp({
    template: el.innerHTML,
    components
  });
  app.mount(el);
}