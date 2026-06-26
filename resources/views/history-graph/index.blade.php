@extends('layouts.app')
@section('title', 'History Graph — GeoArchive')
@section('content')
    <header class="path-intro">
        <p class="eyebrow">Connected history</p>
        <h1>The history graph</h1>
        <p>Every artifact and event in the archive is a node. Lines follow the chronological timeline and the many-to-many links between objects and the events they document. Drag the canvas to explore, scroll to zoom, hover to focus a story, and tap any node to open its full archive entry.</p>
    </header>

    <div class="graph-toolbar">
        <div class="graph-legend">
            <span class="graph-key graph-key--event">Historical event</span>
            <span class="graph-key graph-key--artifact">Artifact</span>
            <span class="graph-stat">{{ $eventCount }} events &middot; {{ $artifactCount }} artifacts &middot; {{ $linkCount }} connections</span>
        </div>
        <button type="button" class="link-button graph-replay" id="graphReplay">Replay layout</button>
    </div>

    <div class="graph-wrap" id="graphWrap">
        <svg id="historyGraph" viewBox="0 0 1000 640" role="img"
             aria-label="Interactive graph linking Georgian historical events and artifacts">
            <g id="graphViewport">
                <g id="graphEdges"></g>
                <g id="graphNodes"></g>
            </g>
        </svg>
        <p class="graph-empty" id="graphEmpty" hidden>No connected records are available yet.</p>
    </div>

    <noscript>
        <p class="graph-fallback">This interactive graph needs JavaScript. You can still explore the same connections through the
            <a href="{{ route('history-paths.index') }}">History Paths</a> page.</p>
    </noscript>

    <script id="graphData" type="application/json">@json(['nodes' => $nodes, 'edges' => $edges])</script>

    <style>
        .graph-toolbar { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; margin: 1.25rem 0 .75rem; }
        .graph-legend { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; font-size: .85rem; }
        .graph-key { display: inline-flex; align-items: center; gap: .45rem; color: var(--muted); }
        .graph-key::before { content: ""; width: .8rem; height: .8rem; border-radius: 50%; }
        .graph-key--event::before { background: var(--wine); }
        .graph-key--artifact::before { background: var(--gold); }
        .graph-stat { color: var(--muted); }
        .graph-replay { font-size: .85rem; }
        .graph-wrap { position: relative; border: 1px solid var(--line); border-radius: 6px; box-shadow: var(--shadow);
            background: radial-gradient(circle at 50% 35%, #fbf8f1, #efe9dd); overflow: hidden; }
        #historyGraph { display: block; width: 100%; height: clamp(420px, 70vh, 720px); touch-action: none; cursor: grab; }
        #historyGraph.is-panning { cursor: grabbing; }
        .graph-edge { stroke: #c9bda3; stroke-width: 1; opacity: .55; }
        .graph-edge.edge--era { stroke: var(--wine); stroke-width: 1.6; opacity: .5; }
        .graph-node { cursor: pointer; }
        .graph-node circle { stroke: #fff; stroke-width: 1.5; transition: r .12s ease; }
        .graph-node.node--event circle { fill: var(--wine); }
        .graph-node.node--artifact circle { fill: var(--gold); }
        .graph-node text { font: 600 8px/1 'Segoe UI', system-ui, sans-serif; fill: var(--ink); paint-order: stroke;
            stroke: rgba(251, 248, 241, .9); stroke-width: 2.4px; pointer-events: none; opacity: .42; }
        .graph-node.is-active circle { stroke: var(--ink); stroke-width: 2; }
        .graph-node.is-active text, .graph-node:hover text { opacity: 1; font-size: 9.5px; }
        svg.has-focus .graph-edge { opacity: .12; }
        svg.has-focus .graph-edge.is-lit { opacity: .9; stroke: var(--gold); }
        svg.has-focus .graph-node { opacity: .28; }
        svg.has-focus .graph-node.is-lit { opacity: 1; }
        svg.has-focus .graph-node.is-lit text { opacity: 1; }
        .graph-empty { position: absolute; inset: 0; display: grid; place-items: center; color: var(--muted); margin: 0; pointer-events: none; }
        .graph-empty[hidden] { display: none; }
        .graph-fallback { margin-top: 1rem; color: var(--muted); }
        @media (max-width: 580px) {
            .graph-toolbar { flex-direction: column; align-items: flex-start; }
            #historyGraph { height: clamp(340px, 64vh, 520px); }
            .graph-legend { gap: .6rem 1rem; }
        }
    </style>

    <script>
        (function () {
            var svg = document.getElementById('historyGraph');
            var raw = document.getElementById('graphData');
            if (!svg || !raw) { return; }

            var data = JSON.parse(raw.textContent || '{"nodes":[],"edges":[]}');
            var nodes = data.nodes || [];
            var edges = data.edges || [];

            if (!nodes.length) {
                document.getElementById('graphEmpty').hidden = false;
                return;
            }

            var W = 1000, H = 640, cx = W / 2, cy = H / 2;
            var byId = {};
            nodes.forEach(function (n, i) {
                // Deterministic starting ring so the layout is stable across loads.
                var a = (i / nodes.length) * Math.PI * 2;
                n.x = cx + Math.cos(a) * 230 + (i % 5) * 6;
                n.y = cy + Math.sin(a) * 230 + (i % 3) * 6;
                n.vx = 0; n.vy = 0;
                n.deg = 0;
                byId[n.id] = n;
            });

            // Keep only edges whose endpoints exist, and record adjacency for hover.
            var adj = {};
            nodes.forEach(function (n) { adj[n.id] = {}; });
            edges = edges.filter(function (e) {
                if (!byId[e.source] || !byId[e.target]) { return false; }
                byId[e.source].deg++; byId[e.target].deg++;
                adj[e.source][e.target] = true;
                adj[e.target][e.source] = true;
                return true;
            });

            var edgesG = document.getElementById('graphEdges');
            var nodesG = document.getElementById('graphNodes');
            var NS = 'http://www.w3.org/2000/svg';

            edges.forEach(function (e) {
                var line = document.createElementNS(NS, 'line');
                line.setAttribute('class', 'graph-edge' + (e.kind === 'era' ? ' edge--era' : ''));
                e._el = line;
                edgesG.appendChild(line);
            });

            nodes.forEach(function (n) {
                var g = document.createElementNS(NS, 'g');
                g.setAttribute('class', 'graph-node node--' + n.type);
                var r = n.type === 'event' ? 8 : 5.5;
                n._r = r + Math.min(4, n.deg * 0.7);
                var c = document.createElementNS(NS, 'circle');
                c.setAttribute('r', n._r);
                var t = document.createElementNS(NS, 'text');
                t.setAttribute('x', 0);
                t.setAttribute('y', -(n._r + 3));
                t.setAttribute('text-anchor', 'middle');
                t.textContent = n.label.length > 34 ? n.label.slice(0, 33) + '…' : n.label;
                g.appendChild(c); g.appendChild(t);
                n._g = g;
                nodesG.appendChild(g);

                g.addEventListener('pointerenter', function () { focus(n); });
                g.addEventListener('pointerleave', function () { if (!dragNode) { clearFocus(); } });
                g.addEventListener('click', function () {
                    if (!moved && n.url) { window.location.href = n.url; }
                });
                attachDrag(g, n);
            });

            // ----- force simulation -----
            var alpha = 1;
            function tick() {
                // repulsion (all pairs)
                for (var i = 0; i < nodes.length; i++) {
                    var a = nodes[i];
                    for (var j = i + 1; j < nodes.length; j++) {
                        var b = nodes[j];
                        var dx = a.x - b.x, dy = a.y - b.y;
                        var d2 = dx * dx + dy * dy || 0.01;
                        var f = 1400 / d2;
                        var d = Math.sqrt(d2);
                        var fx = (dx / d) * f, fy = (dy / d) * f;
                        a.vx += fx; a.vy += fy; b.vx -= fx; b.vy -= fy;
                    }
                }
                // spring attraction along edges
                edges.forEach(function (e) {
                    var a = byId[e.source], b = byId[e.target];
                    var dx = b.x - a.x, dy = b.y - a.y;
                    var d = Math.sqrt(dx * dx + dy * dy) || 0.01;
                    var rest = e.kind === 'era' ? 70 : 95;
                    var f = (d - rest) * 0.02;
                    var fx = (dx / d) * f, fy = (dy / d) * f;
                    a.vx += fx; a.vy += fy; b.vx -= fx; b.vy -= fy;
                });
                // gravity to centre + integrate
                nodes.forEach(function (n) {
                    if (n === dragNode) { return; }
                    n.vx += (cx - n.x) * 0.004;
                    n.vy += (cy - n.y) * 0.004;
                    n.vx *= 0.86; n.vy *= 0.86;
                    n.x += n.vx * alpha; n.y += n.vy * alpha;
                    n.x = Math.max(20, Math.min(W - 20, n.x));
                    n.y = Math.max(20, Math.min(H - 20, n.y));
                });
                render();
                alpha *= 0.992;
                if (alpha > 0.03 || dragNode) { requestAnimationFrame(tick); }
            }

            function render() {
                edges.forEach(function (e) {
                    var a = byId[e.source], b = byId[e.target];
                    e._el.setAttribute('x1', a.x); e._el.setAttribute('y1', a.y);
                    e._el.setAttribute('x2', b.x); e._el.setAttribute('y2', b.y);
                });
                nodes.forEach(function (n) {
                    n._g.setAttribute('transform', 'translate(' + n.x.toFixed(1) + ',' + n.y.toFixed(1) + ')');
                });
            }

            function reheat(v) { alpha = v || 0.5; requestAnimationFrame(tick); }

            // ----- hover focus -----
            function focus(n) {
                svg.classList.add('has-focus');
                nodes.forEach(function (m) {
                    m._g.classList.toggle('is-lit', m === n || adj[n.id][m.id]);
                });
                edges.forEach(function (e) {
                    e._el.classList.toggle('is-lit', e.source === n.id || e.target === n.id);
                });
            }
            function clearFocus() {
                svg.classList.remove('has-focus');
                nodes.forEach(function (m) { m._g.classList.remove('is-lit'); });
                edges.forEach(function (e) { e._el.classList.remove('is-lit'); });
            }

            // ----- pan + zoom (viewport group transform) -----
            var vp = document.getElementById('graphViewport');
            var scale = 1, tx = 0, ty = 0;
            function applyVp() { vp.setAttribute('transform', 'translate(' + tx + ',' + ty + ') scale(' + scale + ')'); }
            var pt = svg.createSVGPoint();
            function toSvg(evt) {
                pt.x = evt.clientX; pt.y = evt.clientY;
                return pt.matrixTransform(svg.getScreenCTM().inverse());
            }
            function toGraph(evt) {
                var s = toSvg(evt);
                return { x: (s.x - tx) / scale, y: (s.y - ty) / scale };
            }

            svg.addEventListener('wheel', function (evt) {
                evt.preventDefault();
                var s = toSvg(evt);
                var factor = evt.deltaY < 0 ? 1.12 : 1 / 1.12;
                var ns = Math.max(0.4, Math.min(3.5, scale * factor));
                tx = s.x - (s.x - tx) * (ns / scale);
                ty = s.y - (s.y - ty) * (ns / scale);
                scale = ns; applyVp();
            }, { passive: false });

            // background pan
            var panning = false, panStart = null;
            svg.addEventListener('pointerdown', function (evt) {
                if (evt.target.closest('.graph-node')) { return; }
                panning = true; svg.classList.add('is-panning');
                panStart = { x: evt.clientX, y: evt.clientY, tx: tx, ty: ty };
                svg.setPointerCapture(evt.pointerId);
            });
            svg.addEventListener('pointermove', function (evt) {
                if (!panning) { return; }
                var k = (svg.viewBox.baseVal.width) / svg.getBoundingClientRect().width;
                tx = panStart.tx + (evt.clientX - panStart.x) * k;
                ty = panStart.ty + (evt.clientY - panStart.y) * k;
                applyVp();
            });
            function endPan(evt) { panning = false; svg.classList.remove('is-panning'); if (evt && evt.pointerId != null) { try { svg.releasePointerCapture(evt.pointerId); } catch (e) {} } }
            svg.addEventListener('pointerup', endPan);
            svg.addEventListener('pointercancel', endPan);

            // ----- node drag -----
            var dragNode = null, moved = false;
            function attachDrag(g, n) {
                g.addEventListener('pointerdown', function (evt) {
                    evt.stopPropagation();
                    dragNode = n; moved = false;
                    g.setPointerCapture(evt.pointerId);
                });
                g.addEventListener('pointermove', function (evt) {
                    if (dragNode !== n) { return; }
                    var p = toGraph(evt);
                    if (Math.abs(p.x - n.x) > 1.5 || Math.abs(p.y - n.y) > 1.5) { moved = true; }
                    n.x = p.x; n.y = p.y; n.vx = 0; n.vy = 0;
                    reheat(0.25);
                });
                function drop(evt) {
                    if (dragNode === n) {
                        dragNode = null;
                        try { g.releasePointerCapture(evt.pointerId); } catch (e) {}
                        clearFocus(); reheat(0.3);
                        // A tap (no drag, no pan) opens the record's detail page.
                        if (!moved && n.url) { window.location.href = n.url; }
                    }
                }
                g.addEventListener('pointerup', drop);
                g.addEventListener('pointercancel', drop);
            }

            document.getElementById('graphReplay').addEventListener('click', function () {
                nodes.forEach(function (n, i) {
                    var a = (i / nodes.length) * Math.PI * 2;
                    n.x = cx + Math.cos(a) * 230; n.y = cy + Math.sin(a) * 230; n.vx = 0; n.vy = 0;
                });
                scale = 1; tx = 0; ty = 0; applyVp();
                reheat(1);
            });

            applyVp();
            reheat(1);
        })();
    </script>
@endsection
