var addressData = null;

function laborimap_initializeMap()
{
	var mymap = L.map('mapid',
	{
		//'zoomControl': false,
		//'zoomSnap': 0,
		//'dragging' : false
	}).setView([34.759,-92.184], 7.6);

	var requestPayload = {
					"type":"SERV", 
					"class":"Service_ClinicalResearch", 
					"parent_route":"", 
					"action":"request_getCatchmentCoords", 
					"request_key":"TODO", 
					"args": JSON.stringify([]),
					};

	$.ajax(
	{
		type: "POST",
		data: requestPayload,
		url: '/' + labori_getRootDir() + '/labori_core/native/php/support/Labori_Router.php',
		cache: false,
		async: false,

		success: function(data)
		{
			addressData = JSON.parse(data);

			addressData = addressData["response"];

			for(var i = 0; i < addressData.length; i++)
			{
				L.circle(addressData[i], {
				    color: 'red',
				    fillColor: '#f03',
				    fillOpacity: 0.1,
				    radius: 10
				}).addTo(mymap);
			}
		}
	});
	
	L.tileLayer('https://api.mapbox.com/styles/v1/hudsoncodyl/cjqy7nfpe8n1w2ssl4303j8gp/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoiaHVkc29uY29keWwiLCJhIjoiY2pxeTdsdjUzMDBxajQxbThqOGhoaDV6cyJ9.k20YxctCL1WiLu5VAiOuww', {
	    attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
	    maxZoom: 13,
	    accessToken: 'pk.eyJ1IjoiaHVkc29uY29keWwiLCJhIjoiY2pxeTdsdjUzMDBxajQxbThqOGhoaDV6cyJ9.k20YxctCL1WiLu5VAiOuww'
	}).addTo(mymap);
}

$(document).ready(function() 
{
	laborimap_initializeMap();
});