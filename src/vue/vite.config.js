import { fileURLToPath, URL } from 'node:url'
import path from 'path'
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
	plugins: [vue()],
	resolve: {
	  alias: {
		vue: 'vue/dist/vue.esm-bundler.js'
	  }
	},
	build: {
	  outDir: '../public/js',
	  emptyOutDir: true,
	  manifest: true,
	  rollupOptions: {
		input: path.resolve(__dirname, 'src/main.js'),
		output: {
		  globals: {
			vue: 'vue',
		  },
		  format: 'es'
		},
	  },
	},
  })
  