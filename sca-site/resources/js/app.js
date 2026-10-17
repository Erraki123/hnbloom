

import Alpine from 'alpinejs';
import * as THREE from 'three';

window.Alpine = Alpine;
Alpine.start();

function initClientSidebar3D() {
    const canvas = document.querySelector('#sidebar-3d');

    if (!canvas || canvas.dataset.initialized === 'true') return;

    canvas.dataset.initialized = 'true';

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(42, 1, 0.1, 100);
    camera.position.set(0, 0, 10);

    const renderer = new THREE.WebGLRenderer({
        canvas,
        alpha: true,
        antialias: true
    });

    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setClearColor(0x000000, 0);

    const gold = new THREE.MeshStandardMaterial({
        color: 0xd9b66e,
        metalness: 0.85,
        roughness: 0.23,
        transparent: true,
        opacity: 0.8
    });

    const paleGold = new THREE.MeshStandardMaterial({
        color: 0xf1d99c,
        metalness: 0.8,
        roughness: 0.28,
        wireframe: true,
        transparent: true,
        opacity: 0.35
    });

    scene.add(new THREE.AmbientLight(0xffffff, 1.3));

    const light = new THREE.PointLight(0xd9b66e, 25, 18);
    light.position.set(2, 1, 4);
    scene.add(light);

    const rimLight = new THREE.PointLight(0xffffff, 8, 15);
    rimLight.position.set(-3, 2, 1);
    scene.add(rimLight);

    const objects = [];

    for (let i = 0; i < 3; i++) {
        const geometry = new THREE.TorusGeometry(1.15 + i * 0.42, 0.009, 8, 120);

        const ring = new THREE.Mesh(geometry, gold);
        ring.position.set(0.9, -1.5, -1.5 - i * 0.2);
        ring.rotation.set(0.5 + i * 0.25, 0.25, i * 0.3);

        scene.add(ring);
        objects.push({
            mesh: ring,
            speed: 0.08 + i * 0.025,
            float: 0.05
        });
    }

    const geometries = [
        new THREE.IcosahedronGeometry(0.34, 0),
        new THREE.BoxGeometry(0.48, 0.48, 0.48),
        new THREE.OctahedronGeometry(0.32, 0)
    ];

    const positions = [
        [-0.85, 1.0, 0],
        [1.05, 1.8, -1],
        [-0.75, -0.25, 0.4]
    ];

    geometries.forEach((geometry, i) => {
        const mesh = new THREE.Mesh(
            geometry,
            i === 1 ? paleGold : gold
        );

        mesh.position.set(...positions[i]);
        mesh.rotation.set(0.4 * i, 0.3 * i, 0);

        scene.add(mesh);
        objects.push({
            mesh,
            speed: 0.2 + i * 0.08,
            float: 0.12
        });
    });

    const particleCount = 140;
    const particlePositions = new Float32Array(particleCount * 3);

    for (let i = 0; i < particleCount; i++) {
        particlePositions[i * 3] = (Math.random() - 0.5) * 7;
        particlePositions[i * 3 + 1] = (Math.random() - 0.5) * 12;
        particlePositions[i * 3 + 2] = -2 - Math.random() * 4;
    }

    const particleGeometry = new THREE.BufferGeometry();
    particleGeometry.setAttribute(
        'position',
        new THREE.BufferAttribute(particlePositions, 3)
    );

    const particleMaterial = new THREE.PointsMaterial({
        color: 0xd9b66e,
        size: 0.025,
        transparent: true,
        opacity: 0.65
    });

    const particles = new THREE.Points(
        particleGeometry,
        particleMaterial
    );

    scene.add(particles);

    let frame = 0;
    let animationId;
    let visible = true;

    const resize = () => {
        const width = canvas.clientWidth;
        const height = canvas.clientHeight;

        if (!width || !height) return;

        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
    };

    const observer = new ResizeObserver(resize);
    observer.observe(canvas);
    resize();

    const visibilityObserver = new IntersectionObserver(([entry]) => {
        visible = entry.isIntersecting;

        if (visible && !animationId) animate();
        if (!visible && animationId) {
            cancelAnimationFrame(animationId);
            animationId = null;
        }
    });

    visibilityObserver.observe(canvas);

    function animate() {
        if (!visible) {
            animationId = null;
            return;
        }

        animationId = requestAnimationFrame(animate);
        frame += 0.008;

        objects.forEach(({ mesh, speed, float }, i) => {
            mesh.rotation.y += speed * 0.008;
            mesh.rotation.x += speed * 0.003;
            mesh.position.y += Math.sin(frame + i) * float * 0.002;
        });

        particles.rotation.y = Math.sin(frame * 0.25) * 0.04;
        light.position.x = 2 + Math.sin(frame) * 0.8;

        renderer.render(scene, camera);
    }

    animate();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initClientSidebar3D, { once: true });
} else {
    initClientSidebar3D();
}

