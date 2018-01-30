jExp
=========

jExp is an open-source AJAX framework which allows users to save external responses from an online questionnaires (e.g., Qualtrics, Limesurvey).

Sometimes it’s necessary to include complex measurement tools, such as a STROOP-task or an implicit association test (IAT), in an online questionnaire. But this often makes it very difficult to save this data into the existing questionnaire. With jExp it’s possible to save this data externally with just a few lines of extra code. jExp is designed in such a way that it’s very easy to merge the original questionnaire data with the data in the jExp storage after data collection.

The jExp framework is written in PHP and uses a MVC structure, thus making it easy to extend or change. It is currently in beta version, which means that although it is fully ready to be used for experiments, there are features that need improvement and testing.

General Use
============

The jExp framework consists of two parts:

An experiment manager from where all experiment data can be downloaded.
A listener which is able to save data from an external AJAX request.
Experiment Manager

In the jExp Experiment Manager, users can create new experiments, or download data stored in existing experiments. In addition, users can add new users who need access to the experiment manager as well.

Experiment Manager
------------------

In the jExp Experiment Manager, users can create new experiments, or download data stored in existing experiments. In addition, users can add new users who need access to the experiment manager as well.
