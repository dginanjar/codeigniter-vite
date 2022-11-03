import { defineConfig, loadEnv } from 'vite'

export default defineConfig(() => {
  const env = loadEnv(null, process.cwd(), '')

  return {
    base: '/',

    build: {
      manifest: true,
      rollupOptions: {
        input: `${env.VITE_INPUT}`,
      },
      outDir: `${env.VITE_OUTDIR}`,
      emptyOutDir: false,
      copyPublicDir: false,
    },

    server: {
      origin: 'http://127.0.0.1:5173'
    },
  }
})