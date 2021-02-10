XDEBUG ?= 0

__XDEBUG_PLATFORM = $(shell [ "1" == "$(XDEBUG)" ] && uname -s | tr 'a-z' 'A-Z' || echo "OFF")

# If you run on Windows and have the Windows Subsystem for Linux available, this works too.
ifeq ($(findstring Microsoft,$(shell uname -a)), Microsoft)
__XDEBUG_PLATFORM = WINDOWS
endif

__XDEBUG_HOST_DARWIN = docker.mac.for.localhost
__XDEBUG_HOST_LINUX = $(shell ip -f inet addr show docker0 | grep -Po 'inet \K[\d.]+')
__XDEBUG_HOST_WINDOWS = docker.for.win.localhost
__XDEBUG_HOST_OFF = disabled

# This variable now contains the xdebug host appropriate for the host OS this is running on
# Exported so docker-compose can pass it into the container
export XDEBUG_HOST = $(__XDEBUG_HOST_$(__XDEBUG_PLATFORM))

ifeq ($(XDEBUG),1)
# And this line makes sure we enable xdebug in the PHP container when the XDEBUG environment variable is set to 1
DOCKER_COMPOSE += --file infra/docker-compose.xdebug.yml
endif
