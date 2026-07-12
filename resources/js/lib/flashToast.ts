import { router } from '@inertiajs/vue3';
import { toast } from 'vue-sonner';
import type { FlashToast } from '@/types/ui';

export function initializeFlashToast(): void {
  router.on('flash', (event) => {
    const data = (event as CustomEvent).detail?.flash?.toast as FlashToast | undefined;

    if (data) {
      toast[data.type](data.message);
    }
  });

  router.on('success', (event) => {
    const data = (event as CustomEvent).detail?.page?.props?.flash?.toast as FlashToast | undefined;

    if (data) {
      toast[data.type](data.message);
    }
  });
}