import { createApp } from 'vue'
import WaveUI from 'wave-ui'
import 'wave-ui/dist/wave-ui.css'

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
  app.use(WaveUI, {});
  app.mount(el);
}