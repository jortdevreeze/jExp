jExp
=========

jExp is an open-source AJAX framework which allows users to save external responses from an online questionnaires (e.g., Qualtrics, Limesurvey).

Sometimes it’s necessary to include complex measurement tools, such as a STROOP-task or an implicit association test (IAT), in an online questionnaire. But this often makes it very difficult to save this data into the existing questionnaire. With jExp it’s possible to save this data externally with just a few lines of extra code. jExp is designed in such a way that it’s very easy to merge the original questionnaire data with the data in the jExp storage after data collection.

The jExp framework is written in PHP and uses a MVC structure, thus making it easy to extend or change. It is currently in beta version, which means that although it is fully ready to be used for experiments, there are features that need improvement and testing.

General Use
============

The jExp framework consists of two parts:

* An experiment manager from where all experiment data can be downloaded.
* A listener which is able to save data from an external AJAX request.
Experiment Manager

In the jExp Experiment Manager, users can create new experiments, or download data stored in existing experiments. In addition, users can add new users who need access to the experiment manager as well.

Experiment Manager
------------------

In the jExp Experiment Manager, users can create new experiments, or download data stored in existing experiments. In addition, users can add new users who need access to the experiment manager as well.

<insert image>

When a user creates a new experiment, a unique identifier is automatically generated which is needed for the listener. As soon as the experiment is coupled to the listener, it is able to save external data. An experiment is able to save data from several measurement tools simultaneously, and data for each tool is saved in a unique file with comma separated values (.csv). Each data file consists of several cases from all participants who took part in the online questionnaire. In addition, each case has a reference to the session, thus allowing easy merging of several data files with the original questionnaire data. Once the data collection is ready, users can download an archive (zip-file) with data from each tool.

Each data file will be saved with the given question name and will contain an ID, experiment reference, participant reference, timestamp, tool/question reference, and whether or not an error occurred during data encoding. In addition, each data file will hold all information from the measurement tool in one or more columns, depending on the complexity of the data which the tool generates.

The Listener
------------

The jExp framework is able to receive and save JSON data from an external source. In order to set up the listener you need to include a few lines of code on the page containing the measurement tool:

.. code-block:: js 

	$("#next").bind("click", function(){
		$.ajax({
			crossDomain: true, 
			dataType: 'text',
			url: 'http://www.example.com/json/post/',
			type: 'post',
			data: {
				'experiment': '3395e126f3bfb8fc3812b91d0c685c6a', 
				'identifier': uid, 
				'question': 'sharing',
				'content': JSON.stringify(json)
			}
		});							
	});

To connect to the listener, it needs to know with which experiment it needs to be coupled. In addition it needs to know the name of the question/tool and it needs some reference to identify the current session/participant.

In above example, the page connects to the listener when the #next button is clicked, but this can easily be changed. The script needs to know where the jExp listener is located, which is specified here as example.com. The script connects to experiment with a pre-generated reference number. Each session or participant needs a unique identifier (uid) which can be defined, for example, at the beginning of the experiment. This information is important, because otherwise it’s no longer possible to merge the questionnaire data from one participant with the data stored in jExp. In this example, the measurement tool is named sharing and will be saved accordingly. The actual data content is saved a JSON object. Please note that for the listener to receive data, the data needs to be properly coded as JSON. Below you will find an example from an online IAT saved in proper JSON format:

.. code-block:: js 

	[
		{"iat":1,"key":"left","name":"I","correct":1,"time":39},
		{"iat":2,"key":"left","name":"Self","correct":1,"time":428},
		{"iat":3,"key":"left","name":"My","correct":1,"time":192},
		{"iat":4,"key":"left","name":"Me","correct":1,"time":202},
		{"iat":5,"key":"left","name":"Own","correct":1,"time":205},
		{"iat":6,"key":"left","name":"They","correct":0,"time":464},
		{"iat":7,"key":"left","name":"Them","correct":0,"time":587},
		{"iat":8,"key":"right","name":"Your","correct":1,"time":353},
		{"iat":9,"key":"right","name":"You","correct":1,"time":788},
		{"iat":10,"key":"right","name":"Other","correct":1,"time":453}
	]

Merging Data
============

To be able to use the data and merge it with existing data in SPSS you need to transpose the data first. In the example below we transpose the IAT data from above and in this particular example we’re only interested in peoples’ reaction time. All the other information we will delete when transposing the data. See below for the corresponding SPSS syntax:

.. code-block:: ncl 

CASESTOVARS
 /ID=identifier
 /INDEX = iat
 /RENAME time=item
 /SEPARATOR = ''
 /DROP id experiment timestamp question error key name correct.
LIST.
Once the transposition is done, it’s just a matter of merging this dataset with that of the questionnaire and use the identifier to match the data.
