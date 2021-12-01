 
<svg 
    class="draggable-container"
    xmlns="https://www.w3.org/2000/svg"
    ng-mousedown="mouseDown($event)"
    >
    <g
        ng-repeat="node in chart.nodes"
        ng-attr-transform="translate({{'{{node.x()}}'}}, {{'{{node.y()}}'}})"
        id="node{{'{{node.getId()}}'}}"
        ng-attr-class=""
        tooltip-statitics="node"
        >
        {#        <image x="0" y="0" ng-attr-width="{{'{{node.width()}}'}}" ng-attr-height="{{'{{node.width()}}'}}" xlink:href="" ng-href="{{'{{node.image()}}'}}" />#}
        <image x="0" y="0" ng-attr-width="{{'{{node.width()}}'}}" ng-attr-height="{{'{{node.width()}}'}}" xlink:href="" ng-href="{{'{{node.image()}}'}}" />

        <text
            ng-attr-x="{{'{{node.widthText()}}'}}"
            y="15"
            text-anchor="start"
            alignment-baseline="middle"
            style="font-weight: bold;"
            font-family="sans-serif" 
            >
            {{'{{node.getSendData().textTitle}}'}} 
        </text>
        <text
            ng-attr-x="{{'{{node.widthText()}}'}}"
            y="30"
            text-anchor="start"
            alignment-baseline="middle"
            font-family="sans-serif" 
            >
            {{'{{node.data.sendData.text}}'}}
        </text>
        <text
          ng-attr-x="{{'{{node.widthText()}}'}}"
          y="45"
          text-anchor="start"
          alignment-baseline="middle"
          font-family="sans-serif" 
          >
          {{'{{node.data.sendData.textSubject}}'}}
        </text>

        {#<!-- Edit -->
        <svg  svg-popover ng-model="node.data" ng-connections="chart.connections" ng-click="popoverElementShow(node.data)" xmlns="http://www.w3.org/2000/svg" x="{{'{{node.width() + 5}}'}}" y="-410" viewBox="0 0 300 300"  width="20" >
          <path d="M149.996,0C67.157,0,0.001,67.161,0.001,149.997S67.157,300,149.996,300s150.003-67.163,150.003-150.003    S232.835,0,149.996,0z M221.302,107.945l-14.247,14.247l-29.001-28.999l-11.002,11.002l29.001,29.001l-71.132,71.126    l-28.999-28.996L84.92,186.328l28.999,28.999l-7.088,7.088l-0.135-0.135c-0.786,1.294-2.064,2.238-3.582,2.575l-27.043,6.03    c-0.405,0.091-0.817,0.135-1.224,0.135c-1.476,0-2.91-0.581-3.973-1.647c-1.364-1.359-1.932-3.322-1.512-5.203l6.027-27.035    c0.34-1.517,1.286-2.798,2.578-3.582l-0.137-0.137L192.3,78.941c1.678-1.675,4.404-1.675,6.082,0.005l22.922,22.917    C222.982,103.541,222.982,106.267,221.302,107.945z" fill="#009fb2"/>
        </svg>
    
        <!-- Delete -->
        <svg  ng-click="showDialogDeleted(chart,node.getId())" ng-show="!node.isPrimary()" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="{{'{{node.width() + 30}}'}}" y="-410" version="1.1" viewBox="0 0 44 44" enable-background="new 0 0 44 44" width="20"  class="draggable-container">
          <path d="m22,0c-12.2,0-22,9.8-22,22s9.8,22 22,22 22-9.8 22-22-9.8-22-22-22zm3.2,22.4l7.5,7.5c0.2,0.2 0.3,0.5 0.3,0.7s-0.1,0.5-0.3,0.7l-1.4,1.4c-0.2,0.2-0.5,0.3-0.7,0.3-0.3,0-0.5-0.1-0.7-0.3l-7.5-7.5c-0.2-0.2-0.5-0.2-0.7,0l-7.5,7.5c-0.2,0.2-0.5,0.3-0.7,0.3-0.3,0-0.5-0.1-0.7-0.3l-1.4-1.4c-0.2-0.2-0.3-0.5-0.3-0.7s0.1-0.5 0.3-0.7l7.5-7.5c0.2-0.2 0.2-0.5 0-0.7l-7.5-7.5c-0.2-0.2-0.3-0.5-0.3-0.7s0.1-0.5 0.3-0.7l1.4-1.4c0.2-0.2 0.5-0.3 0.7-0.3s0.5,0.1 0.7,0.3l7.5,7.5c0.2,0.2 0.5,0.2 0.7,0l7.5-7.5c0.2-0.2 0.5-0.3 0.7-0.3 0.3,0 0.5,0.1 0.7,0.3l1.4,1.4c0.2,0.2 0.3,0.5 0.3,0.7s-0.1,0.5-0.3,0.7l-7.5,7.5c-0.2,0.1-0.2,0.5 3.55271e-15,0.7z" fill="#ff2400"/>
        </svg>#}

        <g
            ng-repeat="connector in node.inputConnectors"
            ng-mousedown="connectorMouseDown($event, node, connector, $index, true)"
            class="connector input-connector"
            >
            <text
                ng-attr-x="{{'{{connector.x() + 20}}'}}"
                ng-attr-y="{{'{{connector.y()}}'}}"
                text-anchor="left"
                alignment-baseline="middle"
                >
                {{'{{connector.name()}}'}}
            </text>

            <circle       
                ng-attr-class="{{'{{connector == mouseOverConnector && '~"'mouseover-connector-circle'"~' || '~"'connector-circle'"~'}}'}}"
                ng-attr-r="{{'{{connectorSize}}'}}" 
                ng-attr-cx="{{'{{connector.x()}}'}}"
                ng-attr-cy="{{'{{connector.y()}}'}}"
                />
        </g>

        <g
            ng-repeat="connector in node.outputConnectors"
            ng-mousedown="connectorMouseDown($event, node, connector, $index, false)"
            class="connector output-connector"
            >
            <text
                ng-attr-x="{{'{{connector.x() - 20}}'}}"
                ng-attr-y="{{'{{connector.y()}}'}}"
                text-anchor="end"
                alignment-baseline="middle"
                >
                {{'{{connector.name()}}'}}
            </text>

            <circle             
                ng-attr-class="{{'{{connector == mouseOverConnector && '~"'mouseover-connector-circle'"~' || '~"'connector-circle'"~'}}'}}"
                ng-attr-r="{{'{{connectorSize}}'}}" 
                ng-attr-cx="{{'{{connector.x()}}'}}"
                ng-attr-cy="{{'{{connector.y()}}'}}"
                />
        </g>
    </g>
    <g>
        <g
            ng-repeat="connection in chart.connections"
            class="connection"
            ng-mousedown="connectionMouseDown($event, connection)"
            >
            <path
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-line'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-line'"~' || (connection.data.class == '~"'success'"~' && '~"'connection-line-success'"~' || (connection.data.class == '~"'negation'"~' &&  '~"'connection-line-danger'"~' || '~"'connection-line'"~')))}}'}}"
                ng-attr-d="M {{'{{connection.sourceCoordX()}}'}}, {{'{{connection.sourceCoordY()}}'}}
                C {{'{{connection.sourceTangentX()}}'}}, {{'{{connection.sourceTangentY()}}'}}
                {{'{{connection.destTangentX()}}'}}, {{'{{connection.destTangentY()}}'}}
                {{'{{connection.destCoordX()}}'}}, {{'{{connection.destCoordY()}}'}}"
                >
            </path>

            <text
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-name'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-name'"~' || '~"'connection-name'"~')}}'}}"
                ng-attr-x="{{'{{connection.middleX()}}'}}"
                ng-attr-y="{{'{{connection.middleY()}}'}}"
                text-anchor="middle"
                alignment-baseline="middle"
                >&nbsp;&nbsp;&nbsp; 
            </text>
            <text
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-name'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-name'"~' || '~"'connection-name'"~')}}'}}"
                ng-attr-x="{{'{{connection.middleX()}}'}}"
                ng-attr-y="{{'{{connection.middleY() + 10}}'}}"
                text-anchor="start"
                alignment-baseline="middle"
                style="font-weight: bold;"
                >{{'{{connection.data.sendData.textTitle}}'}}
            </text>
            <text
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-name'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-name'"~' || '~"'connection-name'"~')}}'}}"
                ng-attr-x="{{'{{connection.middleX()}}'}}"
                ng-attr-y="{{'{{connection.middleY() + 30}}'}}"
                text-anchor="start"
                alignment-baseline="middle"
                >{{'{{connection.data.sendData.text}}'}}
            </text>

            <circle
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-endpoint'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-endpoint'"~' || (connection.data.class == '~"'success'"~' && '~"'connection-endpoint-success'"~' || (connection.data.class == '~"'negation'"~' &&  '~"'connection-endpoint-danger'"~' || '~"'connection-endpoint'"~')))}}'}}"
                r="5" 
                ng-attr-cx="{{'{{connection.sourceCoordX()}}'}}" 
                ng-attr-cy="{{'{{connection.sourceCoordY()}}'}}" 
                >
            </circle>

            <circle
                {#        ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-endpoint'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-endpoint'"~' || '~"'connection-endpoint'"~')}}'}}"#}
                ng-attr-class="{{'{{connection.selected() && '~"'selected-connection-endpoint'"~' || (connection == mouseOverConnection && '~"'mouseover-connection-endpoint'"~' || (connection.data.class == '~"'success'"~' && '~"'connection-endpoint-success'"~' || (connection.data.class == '~"'negation'"~' &&  '~"'connection-endpoint-danger'"~' || '~"'connection-endpoint'"~')))}}'}}"
                r="5" 
                ng-attr-cx="{{'{{connection.destCoordX()}}'}}" 
                ng-attr-cy="{{'{{connection.destCoordY()}}'}}" 
                >
            </circle>
        </g>
    </g>

    <g
        ng-if="draggingConnection"
        >
        <path
            class="dragging-connection dragging-connection-line"
            ng-attr-d="M {{'{{dragPoint1.x}}, {{dragPoint1.y}}'}}
            C {{'{{dragTangent1.x}}, {{dragTangent1.y}}'}}
            {{'{{dragTangent2.x}}, {{dragTangent2.y}}'}}
            {{'{{dragPoint2.x}}, {{dragPoint2.y}}'}}"
            >
        </path>

        <circle
            class="dragging-connection dragging-connection-endpoint"
            r="4" 
            ng-attr-cx="{{'{{dragPoint1.x}}'}}" 
            ng-attr-cy="{{'{{dragPoint1.y}}'}}" 
            >
        </circle>

        <circle
            class="dragging-connection dragging-connection-endpoint"
            r="4" 
            ng-attr-cx="{{'{{dragPoint2.x}}'}}" 
            ng-attr-cy="{{'{{dragPoint2.y}}'}}" 
            >
        </circle>
    </g>

    <rect
        ng-if="dragSelecting"
        class="drag-selection-rect"
        ng-attr-x="{{'{{dragSelectionRect.x}}'}}"
        ng-attr-y="{{'{{dragSelectionRect.y}}'}}"
        ng-attr-width="{{'{{dragSelectionRect.width}}'}}"
        ng-attr-height="{{'{{dragSelectionRect.height}}'}}"
        >
    </rect>
</svg>