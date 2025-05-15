/**
 * Three.js Animation for lotteryLK
 * Creates an animated 3D lottery ball simulation
 */

/**
 * Initialize Three.js animation
 * @param {HTMLElement} container - Container element for the animation
 */
function initThreeJsAnimation(container) {
    // Scene setup
    const scene = new THREE.Scene();
    scene.background = new THREE.Color(0xf4f6f7); // Light gray background
    
    // Camera setup
    const camera = new THREE.PerspectiveCamera(
        75, // Field of view
        container.clientWidth / container.clientHeight, // Aspect ratio
        0.1, // Near clipping plane
        1000 // Far clipping plane
    );
    camera.position.z = 20;
    
    // Renderer setup
    const renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);
    
    // Responsive handler
    window.addEventListener('resize', function() {
        const width = container.clientWidth;
        const height = container.clientHeight;
        renderer.setSize(width, height);
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
    });
    
    // Lighting
    const ambientLight = new THREE.AmbientLight(0xffffff, 0.5);
    scene.add(ambientLight);
    
    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.8);
    directionalLight.position.set(0, 10, 10);
    scene.add(directionalLight);
    
    // Create lottery balls
    const balls = [];
    const colors = [
        0x0A3D62, // Primary Blue
        0x1E8449, // Primary Green
        0xF1C40F, // Accent Yellow
        0xE74C3C, // Red
        0x8E44AD, // Purple
        0x3498DB  // Light Blue
    ];
    
    const fontLoader = new THREE.FontLoader();
    
    // Create lottery balls with numbers
    function createLotteryBalls() {
        for (let i = 1; i <= 6; i++) {
            // Ball geometry
            const geometry = new THREE.SphereGeometry(1.5, 32, 32);
            
            // Ball material with random color
            const colorIndex = Math.floor(Math.random() * colors.length);
            const material = new THREE.MeshPhongMaterial({ 
                color: colors[colorIndex],
                shininess: 100
            });
            
            // Create mesh
            const ball = new THREE.Mesh(geometry, material);
            
            // Position randomly within container
            ball.position.x = (Math.random() - 0.5) * 15;
            ball.position.y = (Math.random() - 0.5) * 15;
            ball.position.z = (Math.random() - 0.5) * 5;
            
            // Random velocity for animation
            ball.userData.velocity = {
                x: (Math.random() - 0.5) * 0.1,
                y: (Math.random() - 0.5) * 0.1,
                z: (Math.random() - 0.5) * 0.05
            };
            
            // Random rotation
            ball.rotation.x = Math.random() * Math.PI;
            ball.rotation.y = Math.random() * Math.PI;
            
            // Add to scene and balls array
            scene.add(ball);
            balls.push(ball);
            
            // Add number to the ball (simpler method without text geometry)
            const ballNumber = Math.floor(Math.random() * 49) + 1; // Random number between 1-49
            ball.userData.number = ballNumber;
        }
    }
    
    // Call the function to create balls
    createLotteryBalls();
    
    // Boundaries for the container
    const bounds = {
        xMin: -10,
        xMax: 10,
        yMin: -8,
        yMax: 8,
        zMin: -5,
        zMax: 5
    };
    
    // Animation loop
    function animate() {
        requestAnimationFrame(animate);
        
        // Update ball positions with bounce effect
        balls.forEach(ball => {
            // Update position
            ball.position.x += ball.userData.velocity.x;
            ball.position.y += ball.userData.velocity.y;
            ball.position.z += ball.userData.velocity.z;
            
            // Bounce off walls
            if (ball.position.x <= bounds.xMin || ball.position.x >= bounds.xMax) {
                ball.userData.velocity.x *= -1;
            }
            
            if (ball.position.y <= bounds.yMin || ball.position.y >= bounds.yMax) {
                ball.userData.velocity.y *= -1;
            }
            
            if (ball.position.z <= bounds.zMin || ball.position.z >= bounds.zMax) {
                ball.userData.velocity.z *= -1;
            }
            
            // Slow rotation
            ball.rotation.x += 0.01;
            ball.rotation.y += 0.01;
        });
        
        // Simple collision detection between balls
        for (let i = 0; i < balls.length; i++) {
            const ballA = balls[i];
            
            for (let j = i + 1; j < balls.length; j++) {
                const ballB = balls[j];
                
                // Calculate distance between balls
                const dx = ballB.position.x - ballA.position.x;
                const dy = ballB.position.y - ballA.position.y;
                const dz = ballB.position.z - ballA.position.z;
                const distance = Math.sqrt(dx * dx + dy * dy + dz * dz);
                
                // Check for collision (3 is the sum of radii)
                if (distance < 3) {
                    // Swap velocities for simple elastic collision
                    const tempVelX = ballA.userData.velocity.x;
                    const tempVelY = ballA.userData.velocity.y;
                    const tempVelZ = ballA.userData.velocity.z;
                    
                    ballA.userData.velocity.x = ballB.userData.velocity.x;
                    ballA.userData.velocity.y = ballB.userData.velocity.y;
                    ballA.userData.velocity.z = ballB.userData.velocity.z;
                    
                    ballB.userData.velocity.x = tempVelX;
                    ballB.userData.velocity.y = tempVelY;
                    ballB.userData.velocity.z = tempVelZ;
                }
            }
        }
        
        renderer.render(scene, camera);
    }
    
    // Start animation
    animate();
}
