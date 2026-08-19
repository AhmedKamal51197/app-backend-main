<template>
    <div class="container">
        <h1 class="title">Selfie Photo</h1>
        <div v-if="!isImagesCaptured" id="container" style="position: relative; width: 400px; height: 300px;">
            <video
                id="videoel"
                width="400"
                height="300"
                preload="auto"
                loop
                playsInline
                autoPlay
                @canplay="enablestart"
                style="position: absolute; top: 0; left: 0;"
            ></video>
            <canvas
                id="overlay"
                width="400"
                height="300"
                style="position: absolute; top: 0; left: 0;"
            ></canvas>
        </div>
        <button
            v-if="!isImagesCaptured"
            class="capture-btn"
            :disabled="startDisabled"
            @click="startCapture"
        >
            {{ captureValue }}
        </button>
        <div class="instruction" v-if="!isImagesCaptured">
            1. Press Capture to start
            <br>
            2. Look at the camera your face at the middle, Then press capture
            <br>
            3. Look at the camera your face at the left, Then press capture
            <br>
            4. Look at the camera your face at the right, Then press capture
        </div>
        <div class="captured-images">
            <img v-for="(src, position) in capturedImages" :key="position" :src="src" :alt="position + ' image'"/>
        </div>
        <button
            class="submit-btn"
            :disabled="!isImagesCaptured || loading"
            @click="submitImages"
        >
            {{ loading ? 'Submitting...' : 'Submit' }}
        </button>

    </div>
</template>

<script>
import clm from 'clmtrackr';

export default {
    props: {
        uuid: String,
    },
    data() {
        return {
            vid: null,
            vidWidth: 400,
            vidHeight: 300,
            overlay: null,
            overlayCC: null,
            trackingStarted: false,
            captureValue: "Capture",
            capturing: false,
            instruction: "Please click Capture to begin",
            stage: 0,
            capturedImages: {
                center: null,
                left: null,
                right: null
            },
            loading: false,
            startDisabled: true,
        };
    },
    computed: {
        isImagesCaptured() {
            return this.capturedImages.center && this.capturedImages.left && this.capturedImages.right;
        }
    },
    mounted() {
        this.ctrack = new clm.tracker();
        this.ctrack.init();

        this.vid = document.getElementById('videoel');
        this.overlay = document.getElementById('overlay');
        this.overlayCC = this.overlay.getContext('2d');

        navigator.mediaDevices.getUserMedia({ video: true })
            .then(this.gumSuccess)
            .catch(this.gumFail);
    },
    methods: {
        enablestart() {
            this.captureValue = "Capture";
            this.startDisabled = false;
        },
        adjustVideoProportions() {
            const proportion = this.vid.videoWidth / this.vid.videoHeight;
            const vidWidth = Math.round(this.vidHeight * proportion);
            this.vidWidth = vidWidth;

            this.vid.width = vidWidth;
            this.overlay.width = vidWidth;
        },
        gumSuccess(stream) {
            this.vid.srcObject = stream;

            this.vid.onloadedmetadata = () => {
                this.adjustVideoProportions();
                this.vid.play();
            };

            this.vid.onresize = () => {
                this.adjustVideoProportions();
                if (this.trackingStarted) {
                    this.ctrack.stop();
                    this.ctrack.reset();
                    this.ctrack.start(this.vid);
                }
            };
        },
        gumFail() {
            alert("There was a problem accessing your webcam.");
        },
        startCapture() {
            this.vid.play();
            this.ctrack.start(this.vid);
            this.trackingStarted = true;
            this.capturing = true;
            this.instruction = "Look at the camera";
            this.checkFacePosition();
            this.drawLoop();
        },
        drawLoop() {
            if (!this.isImagesCaptured) {
                requestAnimationFrame(this.drawLoop);
                this.overlayCC.clearRect(0, 0, this.vidWidth, this.vidHeight);

                const positions = this.ctrack.getCurrentPosition();
                if (positions) {
                    this.ctrack.draw(this.overlay); // Draw face detection directly on the overlay canvas
                }
            }
        },
        checkFacePosition() {
            if (this.capturing && !this.isImagesCaptured) {
                const positions = this.ctrack.getCurrentParameters();
                let noseTipX = positions[23]; // Updated to get nose tip x-coordinate
                const leftRange = 0.25;
                const rightRange = 0.5;
                this.captureScreenshot(this.determineFaceDirection(positions));

                if (noseTipX < 0) {
                    noseTipX = Math.abs(noseTipX);
                }

                if (noseTipX === 0) {
                    // TODO: Implement handling when the face is centered
                } else {
                    if (noseTipX < leftRange) {
                        this.captureScreenshot("left");
                        if (this.stage === 1) {
                            this.instruction = "Now, look right";
                        } else if (this.stage === 0) {
                            this.instruction = "Now, look left";
                        }
                    } else if (noseTipX > rightRange) {
                        this.captureScreenshot("right");

                        if (this.stage === 2) {
                            this.instruction = "Capture complete";
                            this.vid.pause();  // Stop the video when done
                            this.capturing = false;
                        } else if (this.stage === 1) {
                            this.instruction = "Now, look right";
                        }
                    } else if (noseTipX > 0.25 && noseTipX < 40) {
                        this.captureScreenshot("center");
                        if (this.stage === 0) {
                            this.instruction = "Now, look left";
                        }
                    }
                }
            }
        },
        captureScreenshot(position) {
            const canvas = document.createElement('canvas');
            canvas.width = this.vidWidth;
            canvas.height = this.vidHeight;
            const context = canvas.getContext('2d');
            context.drawImage(this.vid, 0, 0, this.vidWidth, this.vidHeight);
            if (!this.capturedImages[position]) {
                this.capturedImages[position] = canvas.toDataURL("image/png");
            }

            if (this.isImagesCaptured) {
                this.stopVideoStream();
            }
        },
        determineFaceDirection(positions) {
            const leftEyeX = positions[20];
            const rightEyeX = positions[23];

            const eyeMidpointX = (leftEyeX + rightEyeX) / 2;

            const noseTipX = positions[23];

            const leftThreshold = -10; // Adjust based on your data
            const rightThreshold = 10;  // Adjust based on your data

            if (noseTipX < eyeMidpointX + leftThreshold) {
                return 'right';
            } else if (noseTipX > eyeMidpointX + rightThreshold) {
                return 'left';
            } else {
                return 'center';
            }
        },
        stopVideoStream() {
            const stream = this.vid.srcObject;
            const tracks = stream.getTracks();

            tracks.forEach(track => track.stop());
            this.vid.srcObject = null;
        },
        submitImages() {
            this.loading = true; // Start loading

            const formData = new FormData();
            formData.append('kyc_attachments[0][file]', this.dataURLtoBlob(this.capturedImages.center));
            formData.append('kyc_attachments[0][file_type]', 'selfie_photo');
            formData.append('kyc_attachments[1][file]', this.dataURLtoBlob(this.capturedImages.left));
            formData.append('kyc_attachments[1][file_type]', 'selfie_photo');
            formData.append('kyc_attachments[2][file]', this.dataURLtoBlob(this.capturedImages.right));
            formData.append('kyc_attachments[2][file_type]', 'selfie_photo');

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch(`http://127.0.0.1:8000/api/v1/kycs/${this.uuid}/selfie`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                }
            })
                .then(response => response.json())
                .then(data => {
                    if(data["error"]){
                        alert(data['message']);
                    }else{
                        console.log('Success:', data);
                        alert("Images submitted successfully!");
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                    alert("Failed to submit images.");
                })
                .finally(() => {
                    this.loading = false; // Stop loading after request completes
                });
        },

        dataURLtoBlob(dataURL) {
            const binary = atob(dataURL.split(',')[1]);
            const array = [];
            for (let i = 0; i < binary.length; i++) {
                array.push(binary.charCodeAt(i));
            }
            return new Blob([new Uint8Array(array)], {type: 'image/png'});
        },

        getToken() {
            // Implement a method to retrieve the authorization token if needed
            return localStorage.getItem('authToken');
        }

    }
};
</script>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
}

#container {
    position: relative;
    width: 400px;
    height: 300px;
}

video,
canvas {
    position: absolute;
    top: 0;
    left: 0;
}

.capture-btn {
    margin-top: 10px;
    background-color: blue;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.capture-btn:disabled {
    background-color: gray;
    cursor: not-allowed;
}

.submit-btn {
    margin-top: 10px;
    background-color: green;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.submit-btn:disabled {
    background-color: gray;
    cursor: not-allowed;
}

.instruction {
    margin-top: 10px;
}

.captured-images {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}
</style>
