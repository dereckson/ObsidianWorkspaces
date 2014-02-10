#  _, __,  _, _ __, _  _, _, _
# / \ |_) (_  | | \ | /_\ |\ |
# \ / |_) , ) | |_/ | | | | \|
#  ~  ~    ~  ~ ~   ~ ~ ~ ~  ~
# 
# Unit testing Makefile
#
# It is recommended to copy phpunit.xml in a working directory
# and personalize the storage engine values. Then, you can add
# an environment variable OBSIDIAN_PHPUNIT_CONFIG to this file.
#

OBSIDIAN_PHPUNIT_CONFIG ?= phpunit.xml

all:
	@echo "Tests are currently runnable interpreted. Run them with 'make test'."

test:

	phpunit -c ${OBSIDIAN_PHPUNIT_CONFIG} .
