/* sigmajs.org - an open-source light-weight JavaScript graph drawing library - Version: 0.1 - Author:  Alexis Jacomy - License: MIT */
var sigma={tools:{},classes:{},instances:{}};
(function(){Array.prototype.some||(Array.prototype.some=function(k,n){var g=this.length;if("function"!=typeof k)throw new TypeError;for(var m=0;m<g;m++)if(m in this&&k.call(n,this[m],m,this))return!0;return!1});Array.prototype.forEach||(Array.prototype.forEach=function(k,n){var g=this.length;if("function"!=typeof k)throw new TypeError;for(var m=0;m<g;m++)m in this&&k.call(n,this[m],m,this)});Array.prototype.map||(Array.prototype.map=function(k,n){var g=this.length;if("function"!=typeof k)throw new TypeError;
for(var m=Array(g),q=0;q<g;q++)q in this&&(m[q]=k.call(n,this[q],q,this));return m});Array.prototype.filter||(Array.prototype.filter=function(k,n){var g=this.length;if("function"!=typeof k)throw new TypeError;for(var m=[],q=0;q<g;q++)if(q in this){var u=this[q];k.call(n,u,q,this)&&m.push(u)}return m});Object.keys||(Object.keys=function(){var k=Object.prototype.hasOwnProperty,n=!{toString:null}.propertyIsEnumerable("toString"),g="toString toLocaleString valueOf hasOwnProperty isPrototypeOf propertyIsEnumerable constructor".split(" "),
m=g.length;return function(q){if("object"!==typeof q&&"function"!==typeof q||null===q)throw new TypeError("Object.keys called on non-object");var u=[],t;for(t in q)k.call(q,t)&&u.push(t);if(n)for(t=0;t<m;t++)k.call(q,g[t])&&u.push(g[t]);return u}}())})();
sigma.classes.EventDispatcher=function(){var k={},n=this;this.one=function(g,m){if(!m||!g)return n;("string"==typeof g?g.split(" "):g).forEach(function(g){k[g]||(k[g]=[]);k[g].push({h:m,one:!0})});return n};this.bind=function(g,m){if(!m||!g)return n;("string"==typeof g?g.split(" "):g).forEach(function(g){k[g]||(k[g]=[]);k[g].push({h:m,one:!1})});return n};this.unbind=function(g,m){g||(k={});var q="string"==typeof g?g.split(" "):g;m?q.forEach(function(g){k[g]&&(k[g]=k[g].filter(function(g){return g.h!=
m}));k[g]&&0==k[g].length&&delete k[g]}):q.forEach(function(g){delete k[g]});return n};this.dispatch=function(g,m){k[g]&&(k[g].forEach(function(k){k.h({type:g,content:m,target:n})}),k[g]=k[g].filter(function(g){return!g.one}));return n}};sigma.classes.Cascade=function(){this.p={};this.config=function(k,n){if("string"==typeof k&&void 0==n)return this.p[k];var g="object"==typeof k&&void 0==n?k:{};"string"==typeof k&&(g[k]=n);for(var m in g)void 0!=this.p[m]&&(this.p[m]=g[m]);return this}};
(function(){function k(d,p){function f(){sigma.chronos.removeTask("node_"+c.id,2).removeTask("edge_"+c.id,2).removeTask("label_"+c.id,2).stopTasks();return c}function b(a,b){c.domElements[a]=document.createElement(b);c.domElements[a].style.position="absolute";c.domElements[a].setAttribute("id","sigma_"+a+"_"+c.id);c.domElements[a].setAttribute("class","sigma_"+a+"_"+b);c.domElements[a].setAttribute("width",c.width+"px");c.domElements[a].setAttribute("height",c.height+"px");c.domRoot.appendChild(c.domElements[a]);
return c}function a(){c.p.drawHoverNodes&&(c.graph.checkHover(c.mousecaptor.mouseX,c.mousecaptor.mouseY),c.graph.nodes.forEach(function(a){a.hover&&!a.active&&c.plotter.drawHoverNode(a)}));return c}function l(){c.p.drawActiveNodes&&c.graph.nodes.forEach(function(a){a.active&&c.plotter.drawActiveNode(a)});return c}sigma.classes.Cascade.call(this);sigma.classes.EventDispatcher.call(this);var c=this;this.id=p.toString();this.p={auto:!0,drawNodes:2,drawEdges:1,drawLabels:2,lastNodes:2,lastEdges:0,lastLabels:2,
drawHoverNodes:!0,drawActiveNodes:!0};this.domRoot=d;this.width=this.domRoot.offsetWidth;this.height=this.domRoot.offsetHeight;this.graph=new u;this.domElements={};b("edges","canvas");b("nodes","canvas");b("labels","canvas");b("hover","canvas");b("monitor","div");b("mouse","canvas");this.plotter=new q(this.domElements.nodes.getContext("2d"),this.domElements.edges.getContext("2d"),this.domElements.labels.getContext("2d"),this.domElements.hover.getContext("2d"),this.graph,this.width,this.height);this.monitor=
new m(this,this.domElements.monitor);this.mousecaptor=new g(this.domElements.mouse,this.id);this.mousecaptor.bind("drag interpolate",function(a){c.draw(c.p.auto?2:c.p.drawNodes,c.p.auto?0:c.p.drawEdges,c.p.auto?2:c.p.drawLabels,!0)}).bind("stopdrag stopinterpolate",function(a){c.draw(c.p.auto?2:c.p.drawNodes,c.p.auto?1:c.p.drawEdges,c.p.auto?2:c.p.drawLabels,!0)}).bind("mousedown mouseup",function(a){var b=c.graph.nodes.filter(function(a){return!!a.hover}).map(function(a){return a.id});c.dispatch("mousedown"==
a.type?"downgraph":"upgraph");b.length&&c.dispatch("mousedown"==a.type?"downnodes":"upnodes",b)}).bind("move",function(){c.domElements.hover.getContext("2d").clearRect(0,0,c.domElements.hover.width,c.domElements.hover.height);a();l()});sigma.chronos.bind("startgenerators",function(){sigma.chronos.getGeneratorsIDs().some(function(a){return!!a.match(RegExp("_ext_"+c.id+"$",""))})&&c.draw(c.p.auto?2:c.p.drawNodes,c.p.auto?0:c.p.drawEdges,c.p.auto?2:c.p.drawLabels)}).bind("stopgenerators",function(){c.draw()});
for(var s=0;s<C.plugins.length;s++)C.plugins[s](this);this.draw=function(a,b,d,h){if(h&&sigma.chronos.getGeneratorsIDs().some(function(a){return!!a.match(RegExp("_ext_"+c.id+"$",""))}))return c;a=void 0==a?c.p.drawNodes:a;b=void 0==b?c.p.drawEdges:b;d=void 0==d?c.p.drawLabels:d;h={nodes:a,edges:b,labels:d};c.p.lastNodes=a;c.p.lastEdges=b;c.p.lastLabels=d;f();c.graph.rescale(c.width,c.height,0<a,0<b).setBorders();c.mousecaptor.checkBorders(c.graph.borders,c.width,c.height);c.graph.translate(c.mousecaptor.stageX,
c.mousecaptor.stageY,c.mousecaptor.ratio,0<a,0<b);c.dispatch("graphscaled");for(var l in c.domElements)"canvas"==c.domElements[l].nodeName.toLowerCase()&&(void 0==h[l]||0<=h[l])&&c.domElements[l].getContext("2d").clearRect(0,0,c.domElements[l].width,c.domElements[l].height);c.plotter.currentEdgeIndex=0;c.plotter.currentNodeIndex=0;c.plotter.currentLabelIndex=0;l=null;h=!1;if(a)if(1<a)for(;c.plotter.task_drawNode(););else sigma.chronos.addTask(c.plotter.task_drawNode,"node_"+c.id,!1),h=!0,l="node_"+
c.id;if(d)if(1<d)for(;c.plotter.task_drawLabel(););else l?sigma.chronos.queueTask(c.plotter.task_drawLabel,"label_"+c.id,l):sigma.chronos.addTask(c.plotter.task_drawLabel,"label_"+c.id,!1),h=!0,l="label_"+c.id;if(b)if(1<b)for(;c.plotter.task_drawEdge(););else l?sigma.chronos.queueTask(c.plotter.task_drawEdge,"edge_"+c.id,l):sigma.chronos.addTask(c.plotter.task_drawEdge,"edge_"+c.id,!1),h=!0,l="edge_"+c.id;c.dispatch("draw");c.refresh();h&&sigma.chronos.runTasks();return c};this.resize=function(a,
b){var d=c.width,h=c.height;void 0!=a&&void 0!=b?(c.width=a,c.height=b):(c.width=c.domRoot.offsetWidth,c.height=c.domRoot.offsetHeight);if(d!=c.width||h!=c.height){for(var l in c.domElements)c.domElements[l].setAttribute("width",c.width+"px"),c.domElements[l].setAttribute("height",c.height+"px");c.plotter.resize(c.width,c.height);c.draw(c.p.lastNodes,c.p.lastEdges,c.p.lastLabels,!0)}return c};this.refresh=function(){c.domElements.hover.getContext("2d").clearRect(0,0,c.domElements.hover.width,c.domElements.hover.height);
a();l();return c};this.drawHover=a;this.drawActive=l;this.clearSchedule=f;window.addEventListener("resize",function(){c.resize()})}function n(d){var p=this;sigma.classes.EventDispatcher.call(this);this._core=d;this.kill=function(){};this.getID=function(){return d.id};this.configProperties=function(f,b){var a=d.config(f,b);return a==d?p:a};this.drawingProperties=function(f,b){var a=d.plotter.config(f,b);return a==d.plotter?p:a};this.mouseProperties=function(f,b){var a=d.mousecaptor.config(f,b);return a==
d.mousecaptor?p:a};this.graphProperties=function(f,b){var a=d.graph.config(f,b);return a==d.graph?p:a};this.getMouse=function(){return{mouseX:d.mousecaptor.mouseX,mouseY:d.mousecaptor.mouseY,down:d.mousecaptor.isMouseDown}};this.position=function(f,b,a){if(0==arguments.length)return{stageX:d.mousecaptor.stageX,stageY:d.mousecaptor.stageY,ratio:d.mousecaptor.ratio};d.mousecaptor.stageX=void 0!=f?f:d.mousecaptor.stageX;d.mousecaptor.stageY=void 0!=b?b:d.mousecaptor.stageY;d.mousecaptor.ratio=void 0!=
a?a:d.mousecaptor.ratio;return p};this.goTo=function(f,b,a){d.mousecaptor.interpolate(f,b,a);return p};this.zoomTo=function(f,b,a){a=Math.min(Math.max(d.mousecaptor.config("minRatio"),a),d.mousecaptor.config("maxRatio"));a==d.mousecaptor.ratio?d.mousecaptor.interpolate(f-d.width/2+d.mousecaptor.stageX,b-d.height/2+d.mousecaptor.stageY):d.mousecaptor.interpolate((a*f-d.mousecaptor.ratio*d.width/2)/(a-d.mousecaptor.ratio),(a*b-d.mousecaptor.ratio*d.height/2)/(a-d.mousecaptor.ratio),a);return p};this.resize=
function(f,b){d.resize(f,b);return p};this.draw=function(f,b,a,l){d.draw(f,b,a,l);return p};this.refresh=function(){d.refresh();return p};this.addGenerator=function(f,b,a){sigma.chronos.addGenerator(f+"_ext_"+d.id,b,a);return p};this.removeGenerator=function(f){sigma.chronos.removeGenerator(f+"_ext_"+d.id);return p};this.addNode=function(f,b){d.graph.addNode(f,b);return p};this.addEdge=function(f,b,a,l){d.graph.addEdge(f,b,a,l);return p};this.dropNode=function(f){d.graph.dropNode(f);return p};this.dropEdge=
function(f){d.graph.dropEdge(f);return p};this.pushGraph=function(f,b){f.nodes&&f.nodes.forEach(function(a){!a.id||b&&d.graph.nodesIndex[a.id]||p.addNode(a.id,a)});f.edges&&f.edges.forEach(function(a){validID=a.source&&a.target&&a.id;!validID||b&&d.graph.edgesIndex[a.id]||p.addEdge(a.id,a.source,a.target,a)});return p};this.emptyGraph=function(){d.graph.empty();return p};this.getNodesCount=function(){return d.graph.nodes.length};this.getEdgesCount=function(){return d.graph.edges.length};this.iterNodes=
function(f,b){d.graph.iterNodes(f,b);return p};this.iterEdges=function(f,b){d.graph.iterEdges(f,b);return p};this.getNodes=function(f){return d.graph.getNodes(f)};this.getEdges=function(f){return d.graph.getEdges(f)};this.activateMonitoring=function(){return d.monitor.activate()};this.desactivateMonitoring=function(){return d.monitor.desactivate()};d.bind("downnodes upnodes downgraph upgraph",function(d){p.dispatch(d.type,d.content)});d.graph.bind("overnodes outnodes",function(d){p.dispatch(d.type,
d.content)})}function g(d){function p(b){a.p.mouseEnabled&&(f(a.mouseX,a.mouseY,a.ratio*(0<(void 0!=b.wheelDelta&&b.wheelDelta||void 0!=b.detail&&-b.detail)?a.p.zoomMultiply:1/a.p.zoomMultiply)),a.p.blockScroll&&(b.preventDefault?b.preventDefault():b.returnValue=!1))}function f(c,d,l){a.isMouseDown||(window.clearInterval(a.interpolationID),n=void 0!=l,s=a.stageX,y=c,g=a.stageY,h=d,r=l||a.ratio,r=Math.min(Math.max(r,a.p.minRatio),a.p.maxRatio),w=a.p.directZooming?1-(n?a.p.zoomDelta:a.p.dragDelta):
0,a.ratio==r&&a.stageX==y&&a.stageY==h)||(b(),a.interpolationID=window.setInterval(b,50),a.dispatch("startinterpolate"))}function b(){w+=n?a.p.zoomDelta:a.p.dragDelta;w=Math.min(w,1);var b=sigma.easing.quadratic.easeout(w),c=a.ratio;a.ratio=c*(1-b)+r*b;n?(a.stageX=y+(a.stageX-y)*a.ratio/c,a.stageY=h+(a.stageY-h)*a.ratio/c):(a.stageX=s*(1-b)+y*b,a.stageY=g*(1-b)+h*b);a.dispatch("interpolate");1<=w&&(window.clearInterval(a.interpolationID),b=a.ratio,n?(a.ratio=r,a.stageX=y+(a.stageX-y)*a.ratio/b,a.stageY=
h+(a.stageY-h)*a.ratio/b):(a.stageX=y,a.stageY=h),a.dispatch("stopinterpolate"))}sigma.classes.Cascade.call(this);sigma.classes.EventDispatcher.call(this);var a=this;this.p={minRatio:1,maxRatio:32,marginRatio:1,zoomDelta:0.1,dragDelta:0.3,zoomMultiply:2,directZooming:!1,blockScroll:!0,inertia:1.1,mouseEnabled:!0};var l=0,c=0,s=0,g=0,r=1,y=0,h=0,k=0,m=0,B=0,F=0,w=0,n=!1;this.stageY=this.stageX=0;this.ratio=1;this.mouseY=this.mouseX=0;this.isMouseDown=!1;d.addEventListener("DOMMouseScroll",p,!0);d.addEventListener("mousewheel",
p,!0);d.addEventListener("mousemove",function(b){a.mouseX=void 0!=b.offsetX&&b.offsetX||void 0!=b.layerX&&b.layerX||void 0!=b.clientX&&b.clientX;a.mouseY=void 0!=b.offsetY&&b.offsetY||void 0!=b.layerY&&b.layerY||void 0!=b.clientY&&b.clientY;if(a.isMouseDown){var d=a.mouseX-l+s,h=a.mouseY-c+g;if(d!=a.stageX||h!=a.stageY)m=k,F=B,k=d,B=h,a.stageX=d,a.stageY=h,a.dispatch("drag")}a.dispatch("move");b.preventDefault?b.preventDefault():b.returnValue=!1},!0);d.addEventListener("mousedown",function(b){a.p.mouseEnabled&&
(a.isMouseDown=!0,a.dispatch("mousedown"),s=a.stageX,g=a.stageY,l=a.mouseX,c=a.mouseY,m=k=a.stageX,F=B=a.stageY,a.dispatch("startdrag"),b.preventDefault?b.preventDefault():b.returnValue=!1)},!0);document.addEventListener("mouseup",function(b){a.p.mouseEnabled&&a.isMouseDown&&(a.isMouseDown=!1,a.dispatch("mouseup"),s==a.stageX&&g==a.stageY||f(a.stageX+a.p.inertia*(a.stageX-m),a.stageY+a.p.inertia*(a.stageY-F)),b.preventDefault?b.preventDefault():b.returnValue=!1)},!0);this.checkBorders=function(b,
c,d){return a};this.interpolate=f}function m(d,p){function f(){var a;a="<p>GLOBAL :</p>";for(var d in b.p.globalProbes)a+="<p>"+d+" : "+b.p.globalProbes[d]()+"</p>";a+="<br><p>LOCAL :</p>";for(d in b.p.localProbes)a+="<p>"+d+" : "+b.p.localProbes[d]()+"</p>";b.p.dom.innerHTML=a;return b}sigma.classes.Cascade.call(this);var b=this;this.instance=d;this.monitoring=!1;this.p={fps:40,dom:p,globalProbes:{"Time (ms)":sigma.chronos.getExecutionTime,Queue:sigma.chronos.getQueuedTasksCount,Tasks:sigma.chronos.getTasksCount,
FPS:sigma.chronos.getFPS},localProbes:{"Nodes count":function(){return b.instance.graph.nodes.length},"Edges count":function(){return b.instance.graph.edges.length}}};this.activate=function(){b.monitoring||(b.monitoring=window.setInterval(f,1E3/b.p.fps));return b};this.desactivate=function(){b.monitoring&&(window.clearInterval(b.monitoring),b.monitoring=null,b.p.dom.innerHTML="");return b}}function q(d,p,f,b,a,l,c){function s(a){var c=b,d="fixed"==h.p.labelSize?h.p.defaultLabelSize:h.p.labelSizeRatio*
a.displaySize;c.font=(h.p.hoverFontStyle||h.p.fontStyle||"")+" "+d+"px "+(h.p.hoverFont||h.p.font||"");c.fillStyle="node"==h.p.labelHoverBGColor?a.color||h.p.defaultNodeColor:h.p.defaultHoverLabelBGColor;c.beginPath();h.p.labelHoverShadow&&(c.shadowOffsetX=0,c.shadowOffsetY=0,c.shadowBlur=4,c.shadowColor=h.p.labelHoverShadowColor);sigma.tools.drawRoundRect(c,Math.round(a.displayX-d/2-2),Math.round(a.displayY-d/2-2),Math.round(c.measureText(a.label).width+1.5*a.displaySize+d/2+4),Math.round(d+4),Math.round(d/
2+2),"left");c.closePath();c.fill();c.shadowOffsetX=0;c.shadowOffsetY=0;c.shadowBlur=0;c.beginPath();c.fillStyle="node"==h.p.nodeBorderColor?a.color||h.p.defaultNodeColor:h.p.defaultNodeBorderColor;c.arc(Math.round(a.displayX),Math.round(a.displayY),a.displaySize+h.p.borderSize,0,2*Math.PI,!0);c.closePath();c.fill();c.beginPath();c.fillStyle="node"==h.p.nodeHoverColor?a.color||h.p.defaultNodeColor:h.p.defaultNodeHoverColor;c.arc(Math.round(a.displayX),Math.round(a.displayY),a.displaySize,0,2*Math.PI,
!0);c.closePath();c.fill();c.fillStyle="node"==h.p.labelHoverColor?a.color||h.p.defaultNodeColor:h.p.defaultLabelHoverColor;c.fillText(a.label,Math.round(a.displayX+1.5*a.displaySize),Math.round(a.displayY+d/2-3));return h}function g(a){if(isNaN(a.x)||isNaN(a.y))throw Error("A node's coordinate is not a number (id: "+a.id+")");return!a.hidden&&a.displayX+a.displaySize>-k/3&&a.displayX-a.displaySize<4*k/3&&a.displayY+a.displaySize>-m/3&&a.displayY-a.displaySize<4*m/3}function r(a,b){return b==a||"both"==
b||!b&&(h.p.defaultEdgeArrow==a||"both"==h.p.defaultEdgeArrow)}function y(a,b,c,d,h,l){var f=b[0]-d,s=b[1]-h;c/=Math.sqrt(f*f+s*s);b[0]-=f*c;b[1]-=s*c;a.lineWidth=0;a.fillStyle=a.strokeStyle;sigma.tools.drawArrowhead(a,b[0],b[1],l,sigma.tools.getIncidenceAngle(d,h,b[0],b[1]));return b}sigma.classes.Cascade.call(this);var h=this;this.p={labelColor:"default",defaultLabelColor:"#000",labelHoverBGColor:"default",defaultHoverLabelBGColor:"#fff",labelHoverShadow:!0,labelHoverShadowColor:"#000",labelHoverColor:"default",
defaultLabelHoverColor:"#000",labelActiveBGColor:"default",defaultActiveLabelBGColor:"#fff",labelActiveShadow:!0,labelActiveShadowColor:"#000",labelActiveColor:"default",defaultLabelActiveColor:"#000",labelSize:"fixed",defaultLabelSize:12,labelSizeRatio:2,labelThreshold:6,font:"Arial",hoverFont:"",activeFont:"",fontStyle:"",hoverFontStyle:"",activeFontStyle:"",edgeColor:"source",defaultEdgeColor:"#aaa",defaultEdgeType:"line",defaultEdgeArrow:"none",defaultNodeColor:"#aaa",nodeHoverColor:"node",defaultNodeHoverColor:"#fff",
nodeActiveColor:"node",defaultNodeActiveColor:"#fff",borderSize:0,nodeBorderColor:"node",defaultNodeBorderColor:"#fff",edgesSpeed:200,nodesSpeed:200,labelsSpeed:200};var k=l,m=c;this.currentLabelIndex=this.currentNodeIndex=this.currentEdgeIndex=0;this.task_drawLabel=function(){for(var b=a.nodes.length,c=0;c++<h.p.labelsSpeed&&h.currentLabelIndex<b;)if(h.isOnScreen(a.nodes[h.currentLabelIndex])){var d=a.nodes[h.currentLabelIndex++],l=f;if(d.displaySize>=h.p.labelThreshold||d.forceLabel){var s="fixed"==
h.p.labelSize?h.p.defaultLabelSize:h.p.labelSizeRatio*d.displaySize;l.font=h.p.fontStyle+s+"px "+h.p.font;l.fillStyle="node"==h.p.labelColor?d.color||h.p.defaultNodeColor:h.p.defaultLabelColor;l.fillText(d.label,Math.round(d.displayX+1.5*d.displaySize),Math.round(d.displayY+s/2-3))}}else h.currentLabelIndex++;return h.currentLabelIndex<b};this.task_drawEdge=function(){for(var b=a.edges.length,c,d,l=0;l++<h.p.edgesSpeed&&h.currentEdgeIndex<b;)if(e=a.edges[h.currentEdgeIndex],c=e.source,d=e.target,
e.hidden||c.hidden||d.hidden||!h.isOnScreen(c)&&!h.isOnScreen(d))h.currentEdgeIndex++;else{c=a.edges[h.currentEdgeIndex++];d=[c.source.displayX,c.source.displayY];var f=[c.target.displayX,c.target.displayY],s=c.color;if(!s)switch(h.p.edgeColor){case "source":s=c.source.color||h.p.defaultNodeColor;break;case "target":s=c.target.color||h.p.defaultNodeColor;break;default:s=h.p.defaultEdgeColor}var g=p;switch(c.type||h.p.defaultEdgeType){case "curve":g.strokeStyle=s;var s=(d[0]+f[0])/2+(f[1]-d[1])/4,
k=(d[1]+f[1])/2+(d[0]-f[0])/4;r("source",c.arrow)&&(d=y(g,d,c.source.displaySize,s,k,c.arrowDisplaySize));r("target",c.arrow)&&(f=y(g,f,c.target.displaySize,s,k,c.arrowDisplaySize));g.lineWidth=c.displaySize/3;g.beginPath();g.moveTo(d[0],d[1]);g.quadraticCurveTo(s,k,f[0],f[1]);g.stroke();break;default:g.strokeStyle=s,r("source",c.arrow)&&(d=y(g,d,c.source.displaySize,f[0],f[1],c.arrowDisplaySize)),r("target",c.arrow)&&(f=y(g,f,c.target.displaySize,d[0],d[1],c.arrowDisplaySize)),g.lineWidth=c.displaySize/
3,g.beginPath(),g.moveTo(d[0],d[1]),g.lineTo(f[0],f[1]),g.stroke()}}return h.currentEdgeIndex<b};this.task_drawNode=function(){for(var b=a.nodes.length,c=0;c++<h.p.nodesSpeed&&h.currentNodeIndex<b;)if(h.isOnScreen(a.nodes[h.currentNodeIndex])){var l=a.nodes[h.currentNodeIndex++],f=Math.round(10*l.displaySize)/10,g=d;g.fillStyle=l.color;g.beginPath();g.arc(l.displayX,l.displayY,f,0,2*Math.PI,!0);g.closePath();g.fill();l.hover&&s(l)}else h.currentNodeIndex++;return h.currentNodeIndex<b};this.drawActiveNode=
function(a){var c=b;if(!g(a))return h;var d="fixed"==h.p.labelSize?h.p.defaultLabelSize:h.p.labelSizeRatio*a.displaySize;c.font=(h.p.activeFontStyle||h.p.fontStyle||"")+" "+d+"px "+(h.p.activeFont||h.p.font||"");c.fillStyle="node"==h.p.labelHoverBGColor?a.color||h.p.defaultNodeColor:h.p.defaultActiveLabelBGColor;c.beginPath();h.p.labelActiveShadow&&(c.shadowOffsetX=0,c.shadowOffsetY=0,c.shadowBlur=4,c.shadowColor=h.p.labelActiveShadowColor);sigma.tools.drawRoundRect(c,Math.round(a.displayX-d/2-2),
Math.round(a.displayY-d/2-2),Math.round(c.measureText(a.label).width+1.5*a.displaySize+d/2+4),Math.round(d+4),Math.round(d/2+2),"left");c.closePath();c.fill();c.shadowOffsetX=0;c.shadowOffsetY=0;c.shadowBlur=0;c.beginPath();c.fillStyle="node"==h.p.nodeBorderColor?a.color||h.p.defaultNodeColor:h.p.defaultNodeBorderColor;c.arc(Math.round(a.displayX),Math.round(a.displayY),a.displaySize+h.p.borderSize,0,2*Math.PI,!0);c.closePath();c.fill();c.beginPath();c.fillStyle="node"==h.p.nodeActiveColor?a.color||
h.p.defaultNodeColor:h.p.defaultNodeActiveColor;c.arc(Math.round(a.displayX),Math.round(a.displayY),a.displaySize,0,2*Math.PI,!0);c.closePath();c.fill();c.fillStyle="node"==h.p.labelActiveColor?a.color||h.p.defaultNodeColor:h.p.defaultLabelActiveColor;c.fillText(a.label,Math.round(a.displayX+1.5*a.displaySize),Math.round(a.displayY+d/2-3));return h};this.drawHoverNode=s;this.isOnScreen=g;this.resize=function(a,c){k=a;m=c;return h}}function u(){function d(a){return{x:a.x,y:a.y,size:a.size,degree:a.degree,
inDegree:a.inDegree,outDegree:a.outDegree,displayX:a.displayX,displayY:a.displayY,displaySize:a.displaySize,label:a.label,id:a.id,color:a.color,fixed:a.fixed,active:a.active,hidden:a.hidden,forceLabel:a.forceLabel,attr:a.attr}}function g(a){return{source:a.source.id,target:a.target.id,size:a.size,type:a.type,arrow:a.arrow,weight:a.weight,displaySize:a.displaySize,label:a.label,hidden:a.hidden,id:a.id,attr:a.attr,color:a.color}}function f(){b.nodes=[];b.nodesIndex={};b.edges=[];b.edgesIndex={};return b}
sigma.classes.Cascade.call(this);sigma.classes.EventDispatcher.call(this);var b=this;this.p={minNodeSize:0,maxNodeSize:0,minEdgeSize:0,maxEdgeSize:0,scalingMode:"inside",nodesPowRatio:0.5,edgesPowRatio:0,sideMargin:0,arrowRatio:3};this.borders={};f();this.addNode=function(a,d){if(b.nodesIndex[a])throw Error('Node "'+a+'" already exists.');d=d||{};var c={x:0,y:0,size:1,degree:0,inDegree:0,outDegree:0,fixed:!1,active:!1,hidden:!1,forceLabel:!1,label:a.toString(),id:a.toString(),attr:{}},f;for(f in d)switch(f){case "id":break;
case "x":case "y":case "size":c[f]=+d[f];break;case "fixed":case "active":case "hidden":case "forceLabel":c[f]=!!d[f];break;case "color":case "label":c[f]=d[f];break;default:c.attr[f]=d[f]}b.nodes.push(c);b.nodesIndex[a.toString()]=c;return b};this.addEdge=function(a,d,c,f){if(b.edgesIndex[a])throw Error('Edge "'+a+'" already exists.');if(!b.nodesIndex[d])throw Error("Edge's source \""+d+'" does not exist yet.');if(!b.nodesIndex[c])throw Error("Edge's target \""+c+'" does not exist yet.');f=f||{};
d={source:b.nodesIndex[d],target:b.nodesIndex[c],size:1,weight:1,displaySize:0.5,label:a.toString(),id:a.toString(),hidden:!1,attr:{}};d.source.degree++;d.source.outDegree++;d.target.degree++;d.target.inDegree++;for(var g in f)switch(g){case "id":case "source":case "target":break;case "hidden":d[g]=!!f[g];break;case "size":case "weight":d[g]=+f[g];break;case "color":case "arrow":case "type":d[g]=f[g].toString();break;case "label":d[g]=f[g];break;default:d.attr[g]=f[g]}b.edges.push(d);b.edgesIndex[a.toString()]=
d;return b};this.dropNode=function(a){var d={};((a instanceof Array?a:[a])||[]).forEach(function(a){b.nodesIndex[a]?d[a]=!0:sigma.log('Node "'+a+'" does not exist.')});var c=[];b.nodes.forEach(function(a,b){a.id in d&&(c.unshift(b),0==a.degree&&delete d[a.id])});c.forEach(function(a){b.nodes.splice(a,1)});b.edges=b.edges.filter(function(a){return a.source.id in d?(delete b.edgesIndex[a.id],a.target.degree--,a.target.inDegree--,!1):a.target.id in d?(delete b.edgesIndex[a.id],a.source.degree--,a.source.outDegree--,
!1):!0});return b};this.dropEdge=function(a){((a instanceof Array?a:[a])||[]).forEach(function(a){if(b.edgesIndex[a]){b.edgesIndex[a].source.degree--;b.edgesIndex[a].source.outDegree--;b.edgesIndex[a].target.degree--;b.edgesIndex[a].target.inDegree--;var c=null;b.edges.some(function(b,d){return b.id==a?(c=d,!0):!1});null!=c&&b.edges.splice(c,1);delete b.edgesIndex[a]}else sigma.log('Edge "'+a+'" does not exist.')});return b};this.iterEdges=function(a,d){var c=d?d.map(function(a){return b.edgesIndex[a]}):
b.edges,f=c.map(g);f.forEach(a);c.forEach(function(a,c){var d=f[c],h;for(h in d)switch(h){case "id":case "displaySize":break;case "weight":case "size":a[h]=+d[h];break;case "source":case "target":a[h]=b.nodesIndex[h]||a[h];break;case "hidden":a[h]=!!d[h];break;case "color":case "label":case "arrow":case "type":a[h]=(d[h]||"").toString();break;default:a.attr[h]=d[h]}});return b};this.iterNodes=function(a,f){var c=f?f.map(function(a){return b.nodesIndex[a]}):b.nodes,g=c.map(d);g.forEach(a);c.forEach(function(a,
c){var d=g[c],b;for(b in d)switch(b){case "id":case "attr":case "degree":case "inDegree":case "outDegree":case "displayX":case "displayY":case "displaySize":break;case "x":case "y":case "size":a[b]=+d[b];break;case "fixed":case "active":case "hidden":case "forceLabel":a[b]=!!d[b];break;case "color":case "label":a[b]=(d[b]||"").toString();break;default:a.attr[b]=d[b]}});return b};this.getEdges=function(a){var d=((a instanceof Array?a:[a])||[]).map(function(a){return g(b.edgesIndex[a])});return a instanceof
Array?d:d[0]};this.getNodes=function(a){var f=((a instanceof Array?a:[a])||[]).map(function(a){return d(b.nodesIndex[a])});return a instanceof Array?f:f[0]};this.empty=f;this.rescale=function(a,d,c,f){var g=0,p=0;c&&b.nodes.forEach(function(a){p=Math.max(a.size,p)});f&&b.edges.forEach(function(a){g=Math.max(a.size,g)});var p=p||1,g=g||1,k,h,m,n;c&&b.nodes.forEach(function(a){h=Math.max(a.x,h||a.x);k=Math.min(a.x,k||a.x);n=Math.max(a.y,n||a.y);m=Math.min(a.y,m||a.y)});var q="outside"==b.p.scalingMode?
Math.max(a/Math.max(h-k,1),d/Math.max(n-m,1)):Math.min(a/Math.max(h-k,1),d/Math.max(n-m,1)),t=(b.p.maxNodeSize||p)/q+b.p.sideMargin;h+=t;k-=t;n+=t;m-=t;var q="outside"==b.p.scalingMode?Math.max(a/Math.max(h-k,1),d/Math.max(n-m,1)):Math.min(a/Math.max(h-k,1),d/Math.max(n-m,1)),w,u;b.p.maxNodeSize||b.p.minNodeSize?b.p.maxNodeSize==b.p.minNodeSize?(w=0,u=b.p.maxNodeSize):(w=(b.p.maxNodeSize-b.p.minNodeSize)/p,u=b.p.minNodeSize):(w=1,u=0);var A,E;b.p.maxEdgeSize||b.p.minEdgeSize?(A=b.p.maxEdgeSize==b.p.minEdgeSize?
0:(b.p.maxEdgeSize-b.p.minEdgeSize)/g,E=b.p.minEdgeSize):(A=1,E=0);c&&b.nodes.forEach(function(c){c.displaySize=c.size*w+u;c.fixed||(c.displayX=(c.x-(h+k)/2)*q+a/2,c.displayY=(c.y-(n+m)/2)*q+d/2)});f&&b.edges.forEach(function(a){a.displaySize=a.size*A+E});return b};this.translate=function(a,d,c,f,g){var p=Math.pow(c,b.p.nodesPowRatio);f&&b.nodes.forEach(function(b){b.fixed||(b.displayX=b.displayX*c+a,b.displayY=b.displayY*c+d);b.displaySize*=p});g&&b.edges.forEach(function(a){a.displaySize*=Math.pow(c,
b.p.edgesPowRatio);a.arrowDisplaySize=a.displaySize*b.p.arrowRatio*p});return b};this.setBorders=function(){b.borders={};b.nodes.forEach(function(a){b.borders.minX=Math.min(void 0==b.borders.minX?a.displayX-a.displaySize:b.borders.minX,a.displayX-a.displaySize);b.borders.maxX=Math.max(void 0==b.borders.maxX?a.displayX+a.displaySize:b.borders.maxX,a.displayX+a.displaySize);b.borders.minY=Math.min(void 0==b.borders.minY?a.displayY-a.displaySize:b.borders.minY,a.displayY-a.displaySize);b.borders.maxY=
Math.max(void 0==b.borders.maxY?a.displayY-a.displaySize:b.borders.maxY,a.displayY-a.displaySize)})};this.checkHover=function(a,d){var c,f,g,p=[],k=[];b.nodes.forEach(function(b){if(b.hidden)b.hover=!1;else{c=Math.abs(b.displayX-a);f=Math.abs(b.displayY-d);g=b.displaySize;var m=b.hover,n=c<g&&f<g&&Math.sqrt(c*c+f*f)<g;m&&!n?(b.hover=!1,k.push(b.id)):n&&!m&&(b.hover=!0,p.push(b.id))}});p.length&&b.dispatch("overnodes",p);k.length&&b.dispatch("outnodes",k);return b}}var t=0,C={plugins:[]};sigma.init=
function(d){d=new k(d,(++t).toString());sigma.instances[t]=new n(d);return sigma.instances[t]};sigma.debugMode=0;sigma.log=function(){if(1==sigma.debugMode)for(var d in arguments)console.log(arguments[d]);else if(1<sigma.debugMode)for(d in arguments)throw Error(arguments[d]);return sigma};sigma.tools.drawRoundRect=function(d,g,f,b,a,l,c){l=l?l:0;var k=c?c:[],k="string"==typeof k?k.split(" "):k;c=l&&(0<=k.indexOf("topleft")||0<=k.indexOf("top")||0<=k.indexOf("left"));var m=l&&(0<=k.indexOf("topright")||
0<=k.indexOf("top")||0<=k.indexOf("right")),n=l&&(0<=k.indexOf("bottomleft")||0<=k.indexOf("bottom")||0<=k.indexOf("left")),k=l&&(0<=k.indexOf("bottomright")||0<=k.indexOf("bottom")||0<=k.indexOf("right"));d.moveTo(g,f+l);c?d.arcTo(g,f,g+l,f,l):d.lineTo(g,f);m?(d.lineTo(g+b-l,f),d.arcTo(g+b,f,g+b,f+l,l)):d.lineTo(g+b,f);k?(d.lineTo(g+b,f+a-l),d.arcTo(g+b,f+a,g+b-l,f+a,l)):d.lineTo(g+b,f+a);n?(d.lineTo(g+l,f+a),d.arcTo(g,f+a,g,f+a-l,l)):d.lineTo(g,f+a);d.lineTo(g,f+l)};sigma.tools.drawArrowhead=function(d,
g,f,b,a){d.beginPath();d.moveTo(g,f);var k=g+Math.cos(0.017453292519943295*(22+a))*b,c=f+Math.sin(0.017453292519943295*(22+a))*b,m=g+Math.cos(0.017453292519943295*(a-22))*b;b=f+Math.sin(0.017453292519943295*(a-22))*b;d.lineTo(k,c);d.quadraticCurveTo((g+k+m)/3,(f+c+b)/3,m,b);d.lineTo(g,f);d.fill()};sigma.tools.getRGB=function(d,g){d=d.toString();var f={r:0,g:0,b:0};if(3<=d.length&&"#"==d.charAt(0)){var b=d.length-1;6==b?f={r:parseInt(d.charAt(1)+d.charAt(2),16),g:parseInt(d.charAt(3)+d.charAt(4),16),
b:parseInt(d.charAt(5)+d.charAt(5),16)}:3==b&&(f={r:parseInt(d.charAt(1)+d.charAt(1),16),g:parseInt(d.charAt(2)+d.charAt(2),16),b:parseInt(d.charAt(3)+d.charAt(3),16)})}g&&(f=[f.r,f.g,f.b]);return f};sigma.tools.rgbToHex=function(d,g,f){return sigma.tools.toHex(d)+sigma.tools.toHex(g)+sigma.tools.toHex(f)};sigma.tools.toHex=function(d){d=parseInt(d,10);if(isNaN(d))return"00";d=Math.max(0,Math.min(d,255));return"0123456789ABCDEF".charAt((d-d%16)/16)+"0123456789ABCDEF".charAt(d%16)};sigma.tools.getIncidenceAngle=
function(d,g,f,b){return(d<=f?180:0)+180*Math.atan((b-g)/(f-d))/Math.PI};sigma.chronos=new function(){function d(a){window.setTimeout(a,0);return r}function g(){for(r.dispatch("frameinserted");q&&x.length&&f(););q&&x.length?(A=(new Date).getTime(),u++,E=w-B,F=B-E,r.dispatch("insertframe"),d(g)):a()}function f(){D%=x.length;if(!x[D].task()){var a=x[D].taskName;z=z.filter(function(b){b.taskParent==a&&x.push({taskName:b.taskName,task:b.task});return b.taskParent!=a});r.dispatch("killed",x.splice(D--,
1)[0])}D++;w=(new Date).getTime()-A;return w<=F}function b(){q=!0;u=D=0;C=A=(new Date).getTime();r.dispatch("start");r.dispatch("insertframe");d(g);return r}function a(){r.dispatch("stop");q=!1;return r}function k(a,c,d){if("function"!=typeof a)throw Error('Task "'+c+'" is not a function');x.push({taskName:c,task:a});q=!!(q||d&&b()||1);return r}function c(a){return a?Object.keys(v).filter(function(a){return!!v[a].on}).length:Object.keys(v).length}function m(){Object.keys(v).length?(r.dispatch("startgenerators"),
r.unbind("killed",n),d(function(){for(var a in v)v[a].on=!0,k(v[a].task,a,!1)}),r.bind("killed",n).runTasks()):r.dispatch("stopgenerators");return r}function n(a){void 0!=v[a.content.taskName]&&(v[a.content.taskName].del||!v[a.content.taskName].condition()?delete v[a.content.taskName]:v[a.content.taskName].on=!1,0==c(!0)&&m())}sigma.classes.EventDispatcher.call(this);var r=this,q=!1,h=80,t=0,u=0,B=1E3/h,F=B,w=0,C=0,A=0,E=0,v={},x=[],z=[],D=0;this.frequency=function(a){return void 0!=a?(h=Math.abs(1*
a),B=1E3/h,u=0,r):h};this.runTasks=b;this.stopTasks=a;this.insertFrame=d;this.addTask=k;this.queueTask=function(a,b,c){if("function"!=typeof a)throw Error('Task "'+b+'" is not a function');if(!x.concat(z).some(function(a){return a.taskName==c}))throw Error('Parent task "'+c+'" of "'+b+'" is not attached.');z.push({taskParent:c,taskName:b,task:a});return r};this.removeTask=function(b,c){if(void 0==b)x=[],1==c?z=[]:2==c&&(x=z,z=[]),a();else{var d="string"==typeof b?b:"";x=x.filter(function(a){return("string"==
typeof b?a.taskName==b:a.task==b)?(d=a.taskName,!1):!0});0<c&&(z=z.filter(function(a){1==c&&a.taskParent==d&&x.push(a);return a.taskParent!=d}))}q=!!(!x.length||a()&&0);return r};this.addGenerator=function(a,b,d){if(void 0!=v[a])return r;v[a]={task:b,condition:d};0==c(!0)&&m();return r};this.removeGenerator=function(a){v[a]&&(v[a].on=!1,v[a].del=!0);return r};this.startGenerators=m;this.getGeneratorsIDs=function(){return Object.keys(v)};this.getFPS=function(){q&&(t=Math.round(1E4*(u/((new Date).getTime()-
C)))/10);return t};this.getTasksCount=function(){return x.length};this.getQueuedTasksCount=function(){return z.length};this.getExecutionTime=function(){return A-C};return this};sigma.addPlugin=function(d,g,f){n.prototype[d]=g;C.plugins.push(f)};sigma.easing={linear:{},quadratic:{}};sigma.easing.linear.easenone=function(d){return d};sigma.easing.quadratic.easein=function(d){return d*d};sigma.easing.quadratic.easeout=function(d){return-d*(d-2)};sigma.easing.quadratic.easeinout=function(d){return 1>
(d*=2)?0.5*d*d:-0.5*(--d*(d-2)-1)};sigma.publicPrototype=n.prototype})();