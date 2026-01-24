export class UploadState {
    isUploading = $state(false);
    error = $state<string | null>(null);

    start() {
        this.isUploading = true;
        this.error = null;
    }

    finish() {
        this.isUploading = false;
    }

    fail(message: string) {
        this.isUploading = false;
        this.error = message;
    }

    reset() {
        this.isUploading = false;
        this.error = null;
    }
}

export const uploadState = new UploadState();
